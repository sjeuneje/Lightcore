<?php

namespace App\Controllers;

use Core\Http\Request;
use Core\Http\Response;

class UserController
{
    private array $users = [
        [
            'id' => 1,
            'name' => 'johndoe',
            'age' => 41
        ],
        [
            'id' => 2,
            'name' => 'sjeuneje',
            'age' => 24
        ]
    ];

    public function index(Request $request): Response
    {
        return Response::html(json_encode($this->users))->send();
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer'
        ]);

        $user = [
            'id' => sizeof($this->users) + 1,
            'name' => $request->post('name'),
            'age' => $request->post('age'),
        ];

        $this->users[] = $user;

        return Response::json([
            'message' => 'User created.',
            'data' => $user
        ])->send();
    }

    public function show(Request $request): Response
    {
        foreach ($this->users as $user) {
            if ($user['id'] === (int) $request->input('id')) {
                return Response::html(json_encode($user))->send();
            }
        }

        return Response::html(json_encode([
            'error' => 'Error: user not found',
            'data' => []
        ]))->send();
    }

    public function update(Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'age' => 'required|integer'
        ]);

        foreach ($this->users as &$user) {
            if ($user['id'] === (int) $request->post('id')) {
                if ($request->post('name') !== null) {
                    $user['name'] = $request->post('name');
                }
                if ($request->post('age') !== null) {
                    $user['age'] = $request->post('age');
                }

                return Response::json([
                    'message' => 'User updated.',
                    'data' => $user
                ])->send();
            }
        }

        return Response::html(json_encode([
            'error' => 'Error: user not found',
            'data' => []
        ]))->send();
    }

    public function delete(Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        foreach ($this->users as $key => $user) {
            if ($user['id'] === (int) $request->post('id')) {
                unset($this->users[$key]);
                return Response::json([
                    'message' => 'User deleted.',
                    'data' => $this->users
                ])->send();
            }
        }

        return Response::html(json_encode([
            'error' => 'Error: user not found',
            'data' => []
        ]))->send();
    }
}
