<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\userResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return userResource::collection(
            User::query()->orderBy('id', 'desc')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request ->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response(new userResource($user), status: 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new userResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
       $data = $request->validated();
       if(isset($data['password'])){
           $data['password'] = bcrypt($data['password']);
       }
       $user->update($data);

       return new userResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response("Delete user success", status: 204);
    }
}
