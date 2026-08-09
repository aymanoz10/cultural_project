<?php

use App\Models\Activity;
use App\Models\CulturalCenter;
use App\Models\Reservation;
use App\Models\User;

test('authenticated user can create reservation', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create([
        'cultural_center_id' => $center->id,
        'capacity'           => 10,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/reservations', [
            'activity_id'      => $activity->id,
            'reservation_date' => now()->addDay()->format('Y-m-d'),
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['success' => true])
        ->assertJsonStructure([
            'data' => ['id', 'ticket_id', 'status', 'activity_id'],
        ]);

    $this->assertDatabaseHas('reservations', [
        'user_id'     => $user->id,
        'activity_id' => $activity->id,
        'status'      => 'confirmed',
    ]);
});

test('user is added to wait list when activity is full', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create([
        'cultural_center_id' => $center->id,
        'capacity'           => 1,
    ]);

    Reservation::factory()->create([
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_CONFIRMED,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/reservations', [
            'activity_id'      => $activity->id,
            'reservation_date' => now()->addDay()->format('Y-m-d'),
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['message' => 'تمت إضافتك إلى قائمة الانتظار']);

    $this->assertDatabaseHas('reservations', [
        'user_id'     => $user->id,
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_WAIT_LIST,
    ]);
});

test('user cannot create duplicate reservation', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create([
        'cultural_center_id' => $center->id,
        'capacity'           => 10,
    ]);

    Reservation::factory()->create([
        'user_id'     => $user->id,
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_CONFIRMED,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/reservations', [
            'activity_id'      => $activity->id,
            'reservation_date' => now()->addDay()->format('Y-m-d'),
        ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'لديك حجز مسبق لهذا النشاط']);
});

test('user can list their reservations', function () {
    $user = User::factory()->create();
    Reservation::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->getJson('/api/reservations');

    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
});

test('user can show a specific reservation', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create(['cultural_center_id' => $center->id]);
    $reservation = Reservation::factory()->create([
        'user_id'     => $user->id,
        'activity_id' => $activity->id,
    ]);

    $response = $this->actingAs($user)
        ->getJson("/api/reservations/{$reservation->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $reservation->id);
});

test('user can cancel reservation', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create(['cultural_center_id' => $center->id]);
    $reservation = Reservation::factory()->create([
        'user_id'     => $user->id,
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_CONFIRMED,
    ]);

    $response = $this->actingAs($user)
        ->postJson("/api/reservations/{$reservation->id}/cancel");

    $response->assertOk()
        ->assertJsonFragment(['message' => 'تم إلغاء الحجز']);

    $this->assertDatabaseHas('reservations', [
        'id'     => $reservation->id,
        'status' => Reservation::STATUS_CANCELLED,
    ]);
});

test('cancellation promotes wait list user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create([
        'cultural_center_id' => $center->id,
        'capacity'           => 1,
    ]);

    $confirmed = Reservation::factory()->create([
        'user_id'     => $user1->id,
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_CONFIRMED,
    ]);

    $waitListed = Reservation::factory()->create([
        'user_id'     => $user2->id,
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_WAIT_LIST,
    ]);

    $this->actingAs($user1)
        ->postJson("/api/reservations/{$confirmed->id}/cancel");

    $this->assertDatabaseHas('reservations', [
        'id'     => $waitListed->id,
        'status' => Reservation::STATUS_CONFIRMED,
    ]);
});

test('unauthenticated user cannot create reservation', function () {
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create(['cultural_center_id' => $center->id]);

    $response = $this->postJson('/api/reservations', [
        'activity_id'      => $activity->id,
        'reservation_date' => now()->addDay()->format('Y-m-d'),
    ]);

    $response->assertStatus(401);
});

test('user cannot cancel already cancelled reservation', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create(['cultural_center_id' => $center->id]);
    $reservation = Reservation::factory()->create([
        'user_id'     => $user->id,
        'activity_id' => $activity->id,
        'status'      => Reservation::STATUS_CANCELLED,
    ]);

    $response = $this->actingAs($user)
        ->postJson("/api/reservations/{$reservation->id}/cancel");

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'الحجز ملغى مسبقاً']);
});
