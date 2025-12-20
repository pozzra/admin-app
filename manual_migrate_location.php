<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/vendor/autoload.php';

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    if (Schema::hasTable('users')) {
        if (!Schema::hasColumn('users', 'location')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('location')->nullable()->after('email');
            });
            echo "Column 'location' added to 'users' table successfully.\n";
        } else {
            echo "Column 'location' already exists in 'users' table.\n";
        }
    } else {
        echo "Table 'users' does not exist.\n";
    }

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
