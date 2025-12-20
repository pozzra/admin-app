<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\User;

try {
    $user = User::first();
    if (!$user) {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'User',
            'status' => 'Active'
        ]);
    }

    Order::create([
        'user_id' => $user->id,
        'total_amount' => 150.00,
        'status' => 'Pending',
        'payment_method' => 'Cash'
    ]);

    Order::create([
        'user_id' => $user->id,
        'total_amount' => 300.50,
        'status' => 'Completed',
        'payment_method' => 'Credit Card'
    ]);
    
    Order::create([
        'user_id' => $user->id,
        'total_amount' => 45.00,
        'status' => 'Cancelled',
        'payment_method' => 'PayPal'
    ]);

    echo "Orders seeded successfully.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
