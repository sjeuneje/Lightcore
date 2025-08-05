<?php

namespace App\Controllers;

use Core\Database\DB;
use Core\Http\BaseController;
use Core\Http\Request;
use Core\Http\Response;

class UserController extends BaseController
{
    public function index(): Response
    {
        $data = DB::table('users')
            ->select('users.*', 'tasks.*')
            ->leftJoin('tasks', 'users.id', '=', 'tasks.user_id')
            ->get();

        return Response::json($data);
    }


    public function store(Request $request): Response
    {
        //TODO
    }

    public function show(Request $request): Response
    {
        //TODO
    }

    public function update(Request $request): Response
    {
        //TODO
    }

    public function delete(Request $request): Response
    {
        //TODO
    }
}
