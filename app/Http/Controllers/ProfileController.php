<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = Profile::firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json(['profile' => $profile]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $data = $request->validated();

        $userFields = array_intersect_key($data, array_flip([
            'name',
            'email',
            'phone_number',
        ]));
        if (!empty($userFields)) {
            $user->fill($userFields);
            $user->save();
        }

        $profile->fill($data);
        $profile->save();

        return response()->json([
            'user' => $user->load('profile'),
            'profile' => $profile,
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['required', 'file', 'mimes:jpg,jpeg', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $validated['avatar']->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json(['user' => $user->load('profile')]);
    }

    public function uploadCv(Request $request)
    {
        $validated = $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
        ]);

        $profile = Profile::firstOrCreate(['user_id' => $request->user()->id]);

        if ($profile->cv) {
            Storage::disk('public')->delete($profile->cv);
        }

        $path = $validated['cv']->store('cvs', 'public');
        $profile->update(['cv' => $path]);

        return response()->json(['profile' => $profile]);
    }
}
