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
        $profile = Profile::firstOrCreate(['user_id' => $request->user()->id]);
        $data = $request->validated();

        $profile->fill($data);
        $profile->save();

        return response()->json(['profile' => $profile]);
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
