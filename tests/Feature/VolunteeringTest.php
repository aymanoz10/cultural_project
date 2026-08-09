<?php

use App\Models\Admin;
use App\Models\CulturalCenter;
use App\Models\Volunteering;
use App\Models\VolunteeringActivity;
use App\Models\User;

test('anyone can list volunteering activities', function () {
    VolunteeringActivity::factory()->count(3)->create();

    $response = $this->getJson('/api/volunteering-activities');

    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
});

test('authenticated user can apply for volunteering', function () {
    $user = User::factory()->create();
    $activity = VolunteeringActivity::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/volunteerings', [
            'volunteering_activity_id' => $activity->id,
            'form_data'                => ['experience' => 'خبرة في التنظيم', 'phone' => '0911111111'],
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('volunteerings', [
        'user_id'                  => $user->id,
        'volunteering_activity_id' => $activity->id,
        'status'                   => 'pending',
    ]);
});

test('user cannot apply twice for same volunteering activity', function () {
    $user = User::factory()->create();
    $activity = VolunteeringActivity::factory()->create();

    Volunteering::factory()->create([
        'user_id'                  => $user->id,
        'volunteering_activity_id' => $activity->id,
        'status'                   => 'pending',
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/volunteerings', [
            'volunteering_activity_id' => $activity->id,
            'form_data'                => ['experience' => 'خبرة'],
        ]);

    $response->assertStatus(422)
        ->assertJsonFragment(['message' => 'لقد تقدمت مسبقاً لهذه الفعالية']);
});

test('admin can update volunteering status', function () {
    $admin = Admin::factory()->create();
    $volunteering = Volunteering::factory()->create(['status' => 'pending']);

    $token = $admin->createToken('admin-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->putJson("/api/admin/volunteerings/{$volunteering->id}/status", [
            'status' => 'accepted',
        ]);

    $response->assertOk();

    $this->assertDatabaseHas('volunteerings', [
        'id'     => $volunteering->id,
        'status' => 'accepted',
    ]);
});

test('user can list their volunteerings', function () {
    $user = User::factory()->create();
    Volunteering::factory()->count(2)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->getJson('/api/volunteerings');

    $response->assertOk();
    $this->assertCount(2, $response->json('data'));
});

test('unauthenticated user cannot apply for volunteering', function () {
    $activity = VolunteeringActivity::factory()->create();

    $response = $this->postJson('/api/volunteerings', [
        'volunteering_activity_id' => $activity->id,
        'form_data'                => ['experience' => 'خبرة'],
    ]);

    $response->assertStatus(401);
});
