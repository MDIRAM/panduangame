<?php

use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $this->seed(GameSeeder::class);

    $response = $this->get('/');

    $response->assertStatus(200);
});
