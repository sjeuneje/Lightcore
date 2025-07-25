<?php

namespace app\Controllers;

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

    public function __construct()
    {}

    public function index(Request $request): Response
    {
        return Response::html(json_encode($this->users))->send();
    }

    public function show(Request $request): Response
    {
        foreach ($this->users as $user) {
            if ($user['id'] === (int) $request->input('id'))
                return Response::html(json_encode($user))->send();
        }

        return Response::html(json_encode([
            'error' => 'Error: user not found',
            'data' => []
        ]))->send();
    }
}