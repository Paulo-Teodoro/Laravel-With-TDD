<?php

namespace Tests\Feature\App\Repository\Eloquent;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repository\Eloquent\UserRepository;

class UserRepositoryTest extends TestCase
{
    private $repository;

    protected function setUp() :void
    {
        $this->repository = new UserRepository(new User());

        self::setUp();
    }

    public function test_find_all_empty()
    {
        $response = $this->repository->findAll();
        
        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }

    public function test_find_all()
    {
        User::factory()->count(10)->create();

        $response = $this->repository->findAll();

        $this->assertCount(10, $response);
    }
}