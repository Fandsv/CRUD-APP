<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Profile::all(), 200);
    }

    public function store(ProfileRequest $request): JsonResponse
    {
        $profile = Profile::create($request->validated());
        return response()->json($profile, 201);
    }

    public function show($userId): JsonResponse
    {
        $profile = Profile::where('user_id', $userId)->with('user')->first();

        if (!$profile) {
            return response()->json(['message' => 'Профиль не найден'], 404);
        }

        return response()->json($profile, 200);
    }

    public function update(ProfileRequest $request, $userId): JsonResponse
    {
        $profile = Profile::where('user_id', $userId)->firstOrFail();
        $profile->update($request->validated());
        return response()->json($profile, 200);
    }
}
