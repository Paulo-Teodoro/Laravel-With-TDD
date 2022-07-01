<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    protected string $endpoint = '/api/users';

    /**
     * @dataProvider dataProviderPagination
     */
    public function test_paginate(
        int $total,
        int $page = 1,
        int $totalPage
    )
    {
        User::factory()->count($total)->create();
        $response = $this->getJson("{$this->endpoint}?page={$page}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount($totalPage, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page'
            ],
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email'
                ]
            ]
        ]);
        $response->assertJsonFragment([
            'total' => $total,
            'current_page' => $page
        ]);
    }

    public function dataProviderPagination() :array
    {
        return [
            'test total paginate empty' => ['total' => 0, 'page' => 1, 'totalPage' => 0],
            'test total 10 users page one' => ['total' => 10, 'page' => 1, 'totalPage' => 10],
            'test total 20 users page two' => ['total' => 20, 'page' => 2, 'totalPage' => 5],
            'test total 100 users page three' => ['total' => 100, 'page' => 3, 'totalPage' => 15]
        ];
    }

    /**
     * @dataProvider dataProviderCreate
     */
    public function test_create(
        array $payload,
        int $statusCode,
        array $responseStructure = []
    )
    {
        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus($statusCode);
        $response->assertJsonStructure($responseStructure);
    }

    public function dataProviderCreate() :array
    {
        return [
            'test create' => [
                'payload' => [
                    'name' => 'Paulo Teodoro',
                    'email' => 'pauloteodoroti@gmail.com',
                    'password' => '12345678'
                ], 
                'statusCode' => Response::HTTP_CREATED, 
                'responseStructure' => [
                    'data' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ],
            'test create fail validation' => [
                'payload' => [
                    'email' => 'pauloteodoroti@gmail.com',
                    'password' => '12345678'
                ], 
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'responseStructure' => [
                    'errors' => [
                        'name'
                    ]
                ]
            ]
        ];
    }

    public function test_find()
    {
        $user = User::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$user->email}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email'
            ]
        ]);
    }

    public function test_find_fail()
    {
        $response = $this->getJson("{$this->endpoint}/teste@gmail.com");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * @dataProvider dataProviderUpdate
     */
    public function test_update(
        array $payload,
        int $statusCode,
        array $responseStructure = []
    )
    {
        $user = User::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$user->email}", $payload);

        $response->assertStatus($statusCode);
        $response->assertJsonStructure($responseStructure);
    }

    public function dataProviderUpdate() :array
    {
        return [
            'test update' => [
                'payload' => [
                    'name' => 'Paulo Teodoro',
                    'password' => 'password'
                ], 
                'statusCode' => Response::HTTP_OK, 
                'responseStructure' => [
                    'data' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ],
            'test update without name' => [
                'payload' => [
                    'password' => 'password'
                ], 
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY, 
                'responseStructure' => [
                    'errors' => [
                        'name'
                    ]
                ]
            ],
            'test update short password' => [
                'payload' => [
                    'name' => 'Paulo',
                    'password' => 'ph'
                ], 
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY, 
                'responseStructure' => [
                    'errors' => [
                        'password'
                    ]
                ]
            ],
        ];
    }

    public function test_update_not_found()
    {
        $payload = [
            'name' => 'Paulo Teodoro',
            'password' => 'password'
        ];

        $response = $this->putJson("{$this->endpoint}/blah@teste.com", $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
