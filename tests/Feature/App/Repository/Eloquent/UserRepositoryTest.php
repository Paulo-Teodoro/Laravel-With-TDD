<?php

namespace Tests\Feature\App\Repository\Eloquent;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repository\Eloquent\UserRepository;
use App\Repository\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class UserRepositoryTest extends TestCase
{
    private $repository;

    protected function setUp() :void
    {
        $this->repository = new UserRepository(new User());

        parent::setUp();
    }

    public function test_implements_interface()
    {
        $this->assertInstanceOf(UserRepositoryInterface::class, $this->repository);
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

    public function test_store()
    {
        $response = $this->repository->store([
                        'name'  => 'Paulo Teodoro',
                        'email' => 'pauloteodoroti@gmail.com', 
                        'password' => bcrypt('password')
                    ]);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'email' => 'pauloteodoroti@gmail.com'
        ]);
    }

    public function test_store_exception()
    {
        $this->expectException(QueryException::class);

        $this->repository->store([
            'name'  => 'Paulo Teodoro',
            'password' => bcrypt('password')
        ]);
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $response = $this->repository->update($user->email, [
            'name' => 'Paulo'
        ]);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'name' => 'Paulo'
        ]);
    }

    public function test_update_exception()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->update('123', [
            'name' => 'Paulo'
        ]);
    }

    public function test_delete()
    {
        $user = User::factory()->create();

        $response = $this->repository->delete($user->email);

        $this->assertTrue($response);
    }

    public function test_delete_exception()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->delete('blah@blah.com');
    }
}
