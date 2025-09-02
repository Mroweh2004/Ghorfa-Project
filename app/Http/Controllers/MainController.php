<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Listing;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    function homePage(){
        return view("home");
    }

    function profileInfo(){
        return view(view: "profile.info");
    }    
    function updateProfile(Request $request){
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255', Rule::unique('users')->ignore($user->id)],
            'phone_nb' => ['required','string','max:30', Rule::unique('users')->ignore($user->id)],
            'date_of_birth' => ['nullable','date','before:today'],
            'address' => ['nullable','string'],
            'is_landlord' => ['boolean'],
        ]);
        if ($request->filled('dob_day') && $request->filled('dob_month') && $request->filled('dob_year')) {
            $validated['date_of_birth'] = $request->dob_year . '-' . 
                                        str_pad($request->dob_month, 2, '0', STR_PAD_LEFT) . '-' . 
                                        str_pad($request->dob_day, 2, '0', STR_PAD_LEFT);
        }

        $validated['is_landlord'] = $request->has('is_landlord');

        if ($request->hasFile('profile_image')) {
            if (!empty($user->profile_image) && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles','public');
            $validated['profile_image'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profileInfo')->with('success', 'Profile updated successfully.');
    }
    public function updateProfilePhoto(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'profile_image' => ['required','image','mimes:jpeg,png,jpg,webp','max:2048'],
        ]);

        if ($request->hasFile('profile_image')) {
            if (!empty($user->profile_image) && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles','public');
            $user->update(['profile_image' => $path]);
        }

        return redirect()->route('profileInfo')->with('success', 'Profile photo updated.');
    }
    function profileProperties(){
        $properties = Property::where('user_id', auth()->id())->paginate(12);
        return view("profile.properties", compact('properties'));
    }
    function profileFavorites(){
        $properties = auth()->user()->likedProperties()->paginate(12);
        return view("profile.favorites", compact('properties'));   
    }
    function profileSecurity (){
        return view("profile.security");    
    }

    function searchPage(){
        $properties = Property::paginate(12);
        return view("search", compact('properties'));
    }

    function propertyPage(){
        return view("list-property");
    }
}
    
  
   

