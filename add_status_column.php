<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (!Schema::hasColumn('products', 'status')) {
        Schema::table('products', function (Blueprint $table) {
            $table->string('status')->default('Active')->after('stock');
        });
        echo "Status column added successfully.\n";
    } else {
        echo "Status column already exists.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
