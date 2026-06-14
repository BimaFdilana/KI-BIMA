<?php

use App\Models\Auth\UserModel;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = UserModel::factory()->create());

    $this->get('/dashboard')->assertStatus(200);
});