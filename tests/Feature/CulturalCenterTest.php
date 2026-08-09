<?php

use App\Models\Admin;
use App\Models\CulturalCenter;

test('anyone can list cultural centers', function () {
    CulturalCenter::factory()->count(3)->create();

    $response = $this->getJson('/api/centers');

    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
});

test('anyone can search cultural centers by name', function () {
    CulturalCenter::factory()->create(['name' => 'مركز الأمل']);
    CulturalCenter::factory()->create(['name' => 'مركز النور']);

    $response = $this->getJson('/api/centers?search=الأمل');

    $response->assertOk();
    $this->assertCount(1, $response->json());
});

test('anyone can search cultural centers by location', function () {
    CulturalCenter::factory()->create(['location' => 'دمشق']);
    CulturalCenter::factory()->create(['location' => 'حلب']);

    $response = $this->getJson('/api/centers?search=دمشق');

    $response->assertOk();
    $this->assertCount(1, $response->json());
});

test('admin can create cultural center', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->postJson('/api/centers', [
            'name'        => 'مركز جديد',
            'location'    => 'دمشق',
            'description' => 'وصف المركز',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'مركز جديد']);

    $this->assertDatabaseHas('cultural_centers', ['name' => 'مركز جديد']);
});

test('admin can update cultural center', function () {
    $admin = Admin::factory()->create();
    $center = CulturalCenter::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->postJson("/api/centers/{$center->id}", [
            'name' => 'مركز محدث',
        ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'مركز محدث']);
});

test('admin can delete cultural center', function () {
    $admin = Admin::factory()->create();
    $center = CulturalCenter::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->deleteJson("/api/centers/{$center->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('cultural_centers', ['id' => $center->id]);
});

test('unauthenticated user cannot create cultural center', function () {
    $response = $this->postJson('/api/centers', [
        'name'     => 'مركز',
        'location' => 'دمشق',
    ]);

    $response->assertStatus(401);
});

test('unauthenticated user cannot update cultural center', function () {
    $center = CulturalCenter::factory()->create();

    $response = $this->postJson("/api/centers/{$center->id}", [
        'name' => 'test',
    ]);

    $response->assertStatus(401);
});

test('unauthenticated user cannot delete cultural center', function () {
    $center = CulturalCenter::factory()->create();

    $response = $this->deleteJson("/api/centers/{$center->id}");

    $response->assertStatus(401);
});
