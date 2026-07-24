<?php
chdir('/var/www/html');
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$email = 'admin@gorkhali.com';

if (User::where('email', $email)->exists()) {
    echo "User already exists: {$email}\n";
    exit(0);
}

$hash = Hash::make('Admin@12345');

$user = User::query()->create([
    'id' => (string) Str::ulid(),
    'name' => 'Gorkhali Admin',
    'email' => $email,
    'email_verified' => now(),
    'password' => $hash,
    'password_hash' => $hash,
    'role' => 'ADMIN',
    'is_active' => true,
    'language' => 'ne',
    'theme' => 'light',
    'admin_theme' => 'light',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Created admin user: {$user->email} (id={$user->id})\n";