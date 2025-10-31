<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase; // <--- 1. AÑADIR ESTO (Arregla Error 2)

uses(RefreshDatabase::class); // <--- 2. AÑADIR ESTO (Arregla Error 2)

test('un usuario puede ser administrador', function () {
    // 3. CAMBIAR 'is_admin' por 'role' (Arregla Error 1)
    $user = User::factory()->create([
        'role' => 'user', 
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    // Tu modelo usa 'role', no 'is_admin'
    expect($user->role)->toBe('user'); 
    expect($admin->role)->toBe('admin');
});