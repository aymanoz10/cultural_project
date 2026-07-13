<?php

use App\Models\Activity;
use App\Models\CulturalCenter;
use App\Models\Rating;
use App\Models\User;

test('anyone can list ratings', function () {
    Rating::factory()->count(3)->create();

    $response = $this->getJson('/api/ratings');

    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
});

test('authenticated user can create rating', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/ratings', [
            'rateable_type' => 'center',
            'rateable_id'   => $center->id,
            'value'         => 5,
            'comment'       => 'ممتاز',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('ratings', [
        'user_id'       => $user->id,
        'rateable_type' => CulturalCenter::class,
        'rateable_id'   => $center->id,
        'value'         => 5,
    ]);
});

test('user cannot create duplicate rating', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();

    Rating::factory()->create([
        'user_id'       => $user->id,
        'rateable_type' => CulturalCenter::class,
        'rateable_id'   => $center->id,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/ratings', [
            'rateable_type' => 'center',
            'rateable_id'   => $center->id,
            'value'         => 4,
        ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'لقد قيّمت هذا العنصر مسبقاً']);
});

test('user can update their rating', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $rating = Rating::factory()->create([
        'user_id'       => $user->id,
        'rateable_type' => CulturalCenter::class,
        'rateable_id'   => $center->id,
        'value'         => 3,
    ]);

    $response = $this->actingAs($user)
        ->putJson("/api/ratings/{$rating->id}", [
            'value'   => 5,
            'comment' => 'تحديث',
        ]);

    $response->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('ratings', [
        'id'    => $rating->id,
        'value' => 5,
    ]);
});

test('user can delete their rating', function () {
    $user = User::factory()->create();
    $center = CulturalCenter::factory()->create();
    $rating = Rating::factory()->create([
        'user_id'       => $user->id,
        'rateable_type' => CulturalCenter::class,
        'rateable_id'   => $center->id,
    ]);

    $response = $this->actingAs($user)
        ->deleteJson("/api/ratings/{$rating->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('ratings', ['id' => $rating->id]);
});

test('unauthenticated user cannot create rating', function () {
    $center = CulturalCenter::factory()->create();

    $response = $this->postJson('/api/ratings', [
        'rateable_type' => 'center',
        'rateable_id'   => $center->id,
        'value'         => 5,
    ]);

    $response->assertStatus(401);
});
