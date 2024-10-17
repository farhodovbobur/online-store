<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'string', 'email', 'min:5', 'max:255', 'unique:' . User::class],
            'password' => ['required']
        ]);

        $user = User::query()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password
        ]);

        return response()->json([
            'message' => 'User successful created',
            'user'    => new UserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): UserResource|JsonResponse
    {
        $user = User::query()->find($id);

        return $user ? new UserResource($user) :
            response()->json([
                'message' => 'User not found'
            ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'email' => ['nullable', 'string', 'email', 'min:5', 'max:255', 'unique:' . User::class],
        ]);

        $user = User::query()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'name'  => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        return response()->json([
            'message' => 'User successful updated',
            'user'    => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::query()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User successful deleted'
        ]);
    }
}
