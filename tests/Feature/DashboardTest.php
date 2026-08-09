<?php

use App\Models\Admin;
use App\Models\Activity;
use App\Models\CulturalCenter;
use App\Models\Rating;
use App\Models\Reservation;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Volunteering;
use App\Models\VolunteeringActivity;

test('admin can get dashboard stats', function () {
    $admin = Admin::factory()->create();

    User::factory()->count(5)->create();
    Admin::factory()->count(2)->create();
    $center = CulturalCenter::factory()->create();
    Activity::factory()->count(3)->create(['cultural_center_id' => $center->id]);
    VolunteeringActivity::factory()->count(2)->create();
    Volunteering::factory()->count(4)->create();
    Reservation::factory()->count(6)->create();
    Suggestion::factory()->count(3)->create();
    Rating::factory()->count(4)->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/admin/dashboard');

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'data' => [
                'users_count',
                'admins_count',
                'activities_count',
                'volunteering_activities_count',
                'volunteerings_count',
                'reservations_count',
                'confirmed_reservations',
                'wait_list_reservations',
                'suggestions_by_type',
                'average_rating',
                'pending_volunteerings',
            ],
        ]);
});

test('dashboard returns correct user count', function () {
    $admin = Admin::factory()->create();
    User::factory()->count(10)->create();

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/admin/dashboard');

    $response->assertOk()
        ->assertJsonPath('data.users_count', 10);
});

test('dashboard returns correct reservation counts', function () {
    $admin = Admin::factory()->create();

    Reservation::factory()->count(3)->create(['status' => 'confirmed']);
    Reservation::factory()->count(2)->create(['status' => 'wait_list']);
    Reservation::factory()->count(1)->create(['status' => 'cancelled']);

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/admin/dashboard');

    $response->assertOk()
        ->assertJsonPath('data.confirmed_reservations', 3)
        ->assertJsonPath('data.wait_list_reservations', 2)
        ->assertJsonPath('data.reservations_count', 6);
});

test('unauthenticated user cannot access dashboard', function () {
    $response = $this->getJson('/api/admin/dashboard');

    $response->assertStatus(401);
});
