<?php

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('send register OTP returns success', function () {
    $response = $this->postJson('/api/register/send-otp', [
        'phone'        => '0911111111',
        'name'         => 'Test User',
        'date_of_birth' => '2000-01-01',
        'gender'       => 'male',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'phone',
        ]);

    $this->assertDatabaseHas('otps', [
        'phone'   => '963911111111',
        'purpose' => 'register',
    ]);
});

test('send register OTP fails with existing phone', function () {
    User::factory()->create(['phone' => '963911111111']);

    $response = $this->postJson('/api/register/send-otp', [
        'phone'        => '0911111111',
        'name'         => 'Test User',
        'date_of_birth' => '2000-01-01',
        'gender'       => 'male',
    ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'رقم الهاتف مسجل مسبقاً']);
});

test('verify register OTP creates user', function () {
    $code = '123456';
    $hashedCode = Hash::make($code);

    Otp::create([
        'phone'      => '963911111111',
        'code'       => $hashedCode,
        'purpose'    => 'register',
        'payload'    => [
            'name'         => 'New User',
            'date_of_birth' => '2000-01-01',
            'gender'       => 'female',
        ],
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->postJson('/api/register/verify-otp', [
        'phone' => '0911111111',
        'code'  => $code,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'user',
            'token',
        ]);

    $this->assertDatabaseHas('users', [
        'phone' => '963911111111',
        'name'  => 'New User',
    ]);
});

test('verify register OTP fails with wrong code', function () {
    Otp::create([
        'phone'      => '963911111111',
        'code'       => Hash::make('654321'),
        'purpose'    => 'register',
        'payload'    => ['name' => 'Test', 'date_of_birth' => '2000-01-01', 'gender' => 'male'],
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->postJson('/api/register/verify-otp', [
        'phone' => '0911111111',
        'code'  => '123456',
    ]);

    $response->assertStatus(422);
});

test('verify register OTP fails with expired code', function () {
    Otp::create([
        'phone'      => '963911111111',
        'code'       => Hash::make('123456'),
        'purpose'    => 'register',
        'payload'    => ['name' => 'Test', 'date_of_birth' => '2000-01-01', 'gender' => 'male'],
        'expires_at' => now()->subMinutes(5),
    ]);

    $response = $this->postJson('/api/register/verify-otp', [
        'phone' => '0911111111',
        'code'  => '123456',
    ]);

    $response->assertStatus(422);
});

test('verify register OTP fails with already registered phone', function () {
    User::factory()->create(['phone' => '963911111111']);

    Otp::create([
        'phone'      => '963911111111',
        'code'       => Hash::make('123456'),
        'purpose'    => 'register',
        'payload'    => ['name' => 'Test', 'date_of_birth' => '2000-01-01', 'gender' => 'male'],
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->postJson('/api/register/verify-otp', [
        'phone' => '0911111111',
        'code'  => '123456',
    ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'رقم الهاتف مسجل مسبقاً']);
});
