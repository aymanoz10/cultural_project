<?php

use App\Models\Suggestion;
use App\Models\User;

test('authenticated user can list their suggestions', function () {
    $user = User::factory()->create();
    Suggestion::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->getJson('/api/suggestions');

    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
});

test('authenticated user can create suggestion', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/suggestions', [
            'type'    => 'suggestion',
            'content' => 'اقترح تحسين الخدمة',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('suggestions', [
        'user_id' => $user->id,
        'type'    => 'suggestion',
        'content' => 'اقترح تحسين الخدمة',
    ]);
});

test('authenticated user can create complaint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/suggestions', [
            'type'    => 'complaint',
            'content' => 'شكوى من التأخر',
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('suggestions', [
        'type' => 'complaint',
    ]);
});

test('authenticated user can create question', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/suggestions', [
            'type'    => 'question',
            'content' => 'متى يفتح المركز؟',
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('suggestions', [
        'type' => 'question',
    ]);
});

test('user can update their suggestion', function () {
    $user = User::factory()->create();
    $suggestion = Suggestion::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->putJson("/api/suggestions/{$suggestion->id}", [
            'content' => 'محتوى محدث',
        ]);

    $response->assertOk();

    $this->assertDatabaseHas('suggestions', [
        'id'      => $suggestion->id,
        'content' => 'محتوى محدث',
    ]);
});

test('user can delete their suggestion', function () {
    $user = User::factory()->create();
    $suggestion = Suggestion::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->deleteJson("/api/suggestions/{$suggestion->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('suggestions', ['id' => $suggestion->id]);
});

test('unauthenticated user cannot create suggestion', function () {
    $response = $this->postJson('/api/suggestions', [
        'type'    => 'suggestion',
        'content' => 'اقترح',
    ]);

    $response->assertStatus(401);
});
