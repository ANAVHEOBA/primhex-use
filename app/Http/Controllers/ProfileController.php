<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->profile;
        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|max:255',
        ]);

        $profile = auth()->user()->profile;

        if ($request->hasFile('profile_picture')) {
            if ($profile->profile_picture) {
                Storage::delete($profile->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures');
            $profile->profile_picture = $path;
        }

        $profile->name = $request->name;
        $profile->phone_number = $request->phone_number;
        $profile->email = $request->email;
        $profile->save();

        return response()->json($profile);
    }
}
