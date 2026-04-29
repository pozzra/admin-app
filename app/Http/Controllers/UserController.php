<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $users = User::when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        if (\Illuminate\Support\Facades\Auth::user()->role === 'Admin') {
            return view('Admin.user', compact('users', 'search', 'perPage'));
        }
        return view('Users.user', compact('users', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:Admin,User'],
            'status' => ['nullable', 'string', 'in:Active,Inactive'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('user_images'), $imageName);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status ?? 'Active',
            'image' => $imageName,
        ]);

        // Send Telegram Notification
        $this->telegramService->sendMessage("👤🆕 <b>NEW USER CREATED</b>\n\n" .
                                           "🆔 <b>Name:</b> {$request->name}\n" .
                                           "📧 <b>Email:</b> {$request->email}\n" .
                                           "🎖️ <b>Role:</b> {$request->role}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('user')->with('success', 'User created successfully!');
    }

    public function update(Request $request, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'in:Admin,User'],
            'status' => ['nullable', 'string', 'in:Active,Inactive'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status ?? $user->status;
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:8'],
            ]);
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($user->image && file_exists(public_path('user_images/' . $user->image))) {
                unlink(public_path('user_images/' . $user->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('user_images'), $imageName);
            $user->image = $imageName;
        }

        $user->save();

        // Send Telegram Notification
        $this->telegramService->sendMessage("👤🔄 <b>USER UPDATED</b>\n\n" .
                                           "🆔 <b>Name:</b> {$user->name}\n" .
                                           "📧 <b>Email:</b> {$user->email}\n" .
                                           "🔔 <b>Status:</b> {$user->status}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('user')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);
        
        if ($user->image && file_exists(public_path('user_images/' . $user->image))) {
            unlink(public_path('user_images/' . $user->image));
        }

        $userName = $user->name;
        $user->delete();

        // Send Telegram Notification
        $this->telegramService->sendMessage("👤🗑️ <b>USER DELETED</b>\n\n" .
                                           "🆔 <b>Name:</b> {$userName}\n" .
                                           "👨‍💻 <b>Admin:</b> " . Auth::user()->name);

        return redirect()->route('user')->with('success', 'User deleted successfully!');
    }
}
