<?php

use App\Models\Activity;
use App\Models\Admin;
use App\Models\CulturalCenter;

test('anyone can list activities', function () {
    $center = CulturalCenter::factory()->create();
    Activity::factory()->count(3)->create(['cultural_center_id' => $center->id]);

    $response = $this->getJson('/api/activities');

    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
});

test('anyone can search activities by title', function () {
    $center = CulturalCenter::factory()->create();
    Activity::factory()->create(['cultural_center_id' => $center->id, 'title' => 'ورشة عمل']);
    Activity::factory()->create(['cultural_center_id' => $center->id, 'title' => 'محاضرة']);

    $response = $this->getJson('/api/activities?search=ورشة');

    $response->assertOk();
    $this->assertCount(1, $response->json());
});

test('anyone can filter activities by type', function () {
    $center = CulturalCenter::factory()->create();
    Activity::factory()->create(['cultural_center_id' => $center->id, 'type' => 'workshop']);
    Activity::factory()->create(['cultural_center_id' => $center->id, 'type' => 'lecture']);

    $response = $this->getJson('/api/activities?type=workshop');

    $response->assertOk();
    $this->assertCount(1, $response->json());
});

test('anyone can filter activities by center', function () {
    $center1 = CulturalCenter::factory()->create();
    $center2 = CulturalCenter::factory()->create();
    Activity::factory()->create(['cultural_center_id' => $center1->id]);
    Activity::factory()->create(['cultural_center_id' => $center2->id]);

    $response = $this->getJson("/api/activities?center_id={$center1->id}");

    $response->assertOk();
    $this->assertCount(1, $response->json());
});

test('admin can create activity', function () {
    $admin = Admin::factory()->create();
    $center = CulturalCenter::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->postJson('/api/activities', [
            'cultural_center_id' => $center->id,
            'type'               => 'workshop',
            'title'              => 'ورشة عمل جديدة',
            'description'        => 'وصف الفعالية',
            'start_time'         => now()->addDay()->toDateTimeString(),
            'end_time'           => now()->addDays(2)->toDateTimeString(),
            'capacity'           => 20,
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'ورشة عمل جديدة');

    $this->assertDatabaseHas('activities', ['title' => 'ورشة عمل جديدة']);
});

test('admin can update activity', function () {
    $admin = Admin::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create(['cultural_center_id' => $center->id]);

    $response = $this->actingAs($admin, 'admin')
        ->postJson("/api/activities/{$activity->id}", [
            'title' => 'عنوان محدث',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'عنوان محدث');
});

test('admin can delete activity', function () {
    $admin = Admin::factory()->create();
    $center = CulturalCenter::factory()->create();
    $activity = Activity::factory()->create(['cultural_center_id' => $center->id]);

    $response = $this->actingAs($admin, 'admin')
        ->deleteJson("/api/activities/{$activity->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
});

test('unauthenticated user cannot create activity', function () {
    $center = CulturalCenter::factory()->create();

    $response = $this->postJson('/api/activities', [
        'cultural_center_id' => $center->id,
        'type'               => 'workshop',
        'title'              => 'ورشة',
        'description'        => 'وصف',
        'start_time'         => now()->addDay()->toDateTimeString(),
        'end_time'           => now()->addDays(2)->toDateTimeString(),
    ]);

    $response->assertStatus(401);
});
