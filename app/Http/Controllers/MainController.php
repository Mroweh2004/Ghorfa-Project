<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Unit;
use App\Models\User;
use App\Models\Amenity;
use App\Models\Listing;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    function homePage(){
        $propertyController = new PropertyController();
        $popularCities = $propertyController->popularCities(4); 
        
        return view("home", compact('popularCities'));
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
        ]);
        if ($request->filled('dob_day') && $request->filled('dob_month') && $request->filled('dob_year')) {
            $validated['date_of_birth'] = $request->dob_year . '-' . 
                                        str_pad($request->dob_month, 2, '0', STR_PAD_LEFT) . '-' . 
                                        str_pad($request->dob_day, 2, '0', STR_PAD_LEFT);
        }

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
    function profileFavorites(){
        $properties = auth()->user()->likedProperties()->paginate(12);
        return view("profile.favorites", compact('properties'));   
    }
    function profileSecurity (){
        return view("profile.security");    
    }

    /**
     * List current user's transaction requests (as buyer).
     * Buyer can open each one to see the full report and approve/reject contract.
     */
    function profileTransactions()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with('property')
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('profile.transactions', compact('transactions'));
    }

    function searchPage(){
        $properties = Property::paginate(12);
        return view("search", compact('properties'));
    }

    function propertyPage(){
        if (!auth()->check() || (!auth()->user()->isLandlord())) {
            return redirect()->route('home')
                ->with('error', 'Only landlords and admins can list properties. <a href="' . route('landlord.apply') . '">Become a Landlord</a>');
        }

        $amenities = Amenity::all();
        $rules = Rule::all();
        $units= Unit::all();
        return view("list-property", compact("amenities","rules", "units"));
    }
}
    
  
   

