<?php

use App\Models\Admin;

test('admin can register', function () {
    $response = $this->postJson('/api/admin/register', [
        'name'     => 'Test Admin',
        'phone'    => '0911111111',
        'password' => 'password123',
        'role'     => 'admin',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'admin' => ['id', 'name', 'phone', 'role'],
            'token',
        ]);

    $this->assertDatabaseHas('admins', ['phone' => '0911111111']);
});

test('admin cannot register with duplicate phone', function () {
    Admin::factory()->create(['phone' => '0911111111']);

    $response = $this->postJson('/api/admin/register', [
        'name'     => 'Test Admin',
        'phone'    => '0911111111',
        'password' => 'password123',
        'role'     => 'admin',
    ]);

    $response->assertStatus(422);
});

test('admin can login with correct credentials', function () {
    Admin::factory()->create([
        'phone'    => '0911111111',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'phone'    => '0911111111',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'admin' => ['id', 'name', 'phone', 'role'],
            'token',
        ]);
});

test('admin cannot login with wrong password', function () {
    Admin::factory()->create([
        'phone'    => '0911111111',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'phone'    => '0911111111',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

test('admin can get profile', function () {
    $admin = Admin::factory()->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/admin/profile');

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'admin' => ['id', 'name', 'phone', 'role'],
        ]);
});

test('admin can update profile', function () {
    $admin = Admin::factory()->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson('/api/admin/profile', [
            'name' => 'Updated Name',
        ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Updated Name']);
});

test('admin can logout', function () {
    $admin = Admin::factory()->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/admin/logout');

    $response->assertOk();
});

test('super admin can edit another admin', function () {
    $super = Admin::factory()->super()->create();
    $admin = Admin::factory()->create();

    $token = $super->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/admin/admins/{$admin->id}", [
            'name' => 'Edited Name',
        ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Edited Name']);
});

test('non-super admin cannot edit another admin', function () {
    $admin = Admin::factory()->create();
    $other = Admin::factory()->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson("/api/admin/admins/{$other->id}", [
            'name' => 'Hacked Name',
        ]);

    $response->assertStatus(403);
});

test('super admin can delete another admin', function () {
    $super = Admin::factory()->super()->create();
    $admin = Admin::factory()->create();

    $token = $super->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson("/api/admin/admins/{$admin->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('admins', ['id' => $admin->id]);
});

test('non-super admin cannot delete another admin', function () {
    $admin = Admin::factory()->create();
    $other = Admin::factory()->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->deleteJson("/api/admin/admins/{$other->id}");

    $response->assertStatus(403);
});

test('unauthenticated user cannot access admin profile', function () {
    $response = $this->getJson('/api/admin/profile');

    $response->assertStatus(401);
});
