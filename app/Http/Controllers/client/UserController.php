<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();

        // Tìm user theo id
        $user = User::findOrFail($id);
        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address']
        ]);

        return response()->json([

            'data' => new UserResource($user),
            'message' => 'Update successfully',
        ], 200);
    }


}
