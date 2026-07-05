<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function quickBuy(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);
        $totalAmount = $product->price * $request->quantity;

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $totalAmount,
            'status' => 'Pending',
            'payment_method' => 'Cash', // Default for quick buy
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price,
        ]);

        // Send Telegram Notification
        $user = Auth::user();
        $message = "🛒 <b>QUICK ORDER RECEIVED</b>\n\n".
                   "🆔 <b>Order ID:</b> #{$order->id}\n".
                   "👤 <b>Customer:</b> {$user->name}\n".
                   '📍 <b>Location:</b> '.($user->location ?? 'N/A')."\n".
                   '💰 <b>Total Amount:</b> $'.number_format($totalAmount, 2)."\n".
                   "🔔 <b>Status:</b> Pending\n".
                   '📅 <b>Date:</b> '.$order->created_at->format('Y-m-d H:i')."\n\n".
                   "📦 <b>Items:</b>\n- {$product->name} (x{$request->quantity})";

        $this->telegramService->sendMessage($message);

        return back()->with('success', 'Order placed successfully! We will contact you soon.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:Pending,Completed,Cancelled',
            'payment_method' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Calculate total amount & prepare items
        $totalAmount = 0;
        $orderItems = [];
        $itemsListString = '';

        foreach ($request->products as $itemData) {
            $product = \App\Models\Product::find($itemData['id']);
            $quantity = $itemData['quantity'];
            $price = $product->price;

            $totalAmount += $price * $quantity;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
            ];

            $itemsListString .= "- {$product->name} (x{$quantity}) - $".number_format($price * $quantity, 2)."\n";
        }

        $order = Order::create([
            'user_id' => $request->user_id,
            'total_amount' => $totalAmount,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // Send Telegram Notification
        $user = \App\Models\User::find($request->user_id);
        $message = "🛒 <b>NEW ORDER RECEIVED</b>\n\n".
                   "🆔 <b>Order ID:</b> #{$order->id}\n".
                   "👤 <b>Customer:</b> {$user->name}\n".
                   '📍 <b>Location:</b> '.($user->location ?? 'N/A')."\n".
                   '💰 <b>Total Amount:</b> $'.number_format($totalAmount, 2)."\n".
                   "🔔 <b>Status:</b> {$request->status}\n".
                   "💳 <b>Payment:</b> {$request->payment_method}\n".
                   '📅 <b>Date:</b> '.$order->created_at->format('Y-m-d H:i')."\n\n".
                   "📦 <b>Items:</b>\n".$itemsListString;

        $this->telegramService->sendMessage($message);

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $users = \App\Models\User::where('role', 'User')->where('status', 'Active')->get();
        $products = \App\Models\Product::where('status', 'Active')->get(); // Pass products for the create form

        $orders = Order::with(['user', 'items.product']) // Eager load items
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('Order.index', compact('orders', 'search', 'perPage', 'users', 'products'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:Pending,Completed,Cancelled',
            'payment_method' => 'required|string',
            'products' => 'nullable|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::findOrFail($id);

        $order->user_id = $request->user_id;
        $order->status = $request->status;
        $order->payment_method = $request->payment_method;

        // Recalculate Total & Sync Items if products are provided
        $itemsListString = '';
        if ($request->has('products')) {
            $totalAmount = 0;
            $order->items()->delete(); // Clear old items

            foreach ($request->products as $itemData) {
                $product = \App\Models\Product::find($itemData['id']);
                $quantity = $itemData['quantity'];
                $price = $product->price;

                $totalAmount += $price * $quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                $itemsListString .= "- {$product->name} (x{$quantity}) - $".number_format($price * $quantity, 2)."\n";
            }

            $order->total_amount = $totalAmount;
        } else {
            foreach ($order->items as $item) {
                $itemsListString .= "- {$item->product->name} (x{$item->quantity}) - $".number_format($item->price * $item->quantity, 2)."\n";
            }
        }

        $order->save();

        // Send Telegram Notification for Edit
        $user = $order->user;
        $message = "📝 <b>ORDER UPDATED</b>\n\n".
                   "🆔 <b>Order ID:</b> #{$order->id}\n".
                   "👤 <b>Customer:</b> {$user->name}\n".
                   '📍 <b>Location:</b> '.($user->location ?? 'N/A')."\n".
                   '💰 <b>Total Amount:</b> $'.number_format($order->total_amount, 2)."\n".
                   "🔔 <b>Status:</b> {$order->status}\n".
                   "💳 <b>Payment:</b> {$order->payment_method}\n".
                   '👨‍💻 <b>Updated By:</b> '.Auth::user()->name."\n".
                   '📅 <b>Date:</b> '.now()->format('Y-m-d H:i')."\n\n".
                   "📦 <b>Items:</b>\n".$itemsListString;

        $this->telegramService->sendMessage($message);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }
}
