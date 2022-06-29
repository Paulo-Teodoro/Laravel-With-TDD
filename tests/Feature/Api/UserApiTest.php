<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    protected string $endpoint = '/api/users';

    public function test_get_all_empty()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function test_paginate()
    {
        User::factory()->count(20)->create();
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(15, 'data');
    }

    public function test_page_two()
    {
        User::factory()->count(20)->create();
        $response = $this->getJson("{$this->endpoint}?page=2");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(5, 'data');
    }
}
