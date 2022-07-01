<?php

namespace Tests\Feature\Api;

use App\Models\User;
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
}
