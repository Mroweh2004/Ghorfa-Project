<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use function Laravel\Prompts\alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Unique;

class AuthenticationController extends Controller
{
    /*----------------------------login_______________*/
    function loginPage(){
        return view("login");
    }
    function  submitLogin(Request $request){
        $incomingFields = $request->validate([
            'email'=> 'required',
            'password'=> 'required'
        ]);
          
        if(Auth::attempt(['email'=>$incomingFields['email'],'password'=>$incomingFields['password']])){
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return redirect()->route('login')->withErrors(['email' => 'Invalid credentials.']);
    }
   /*-------------------------logout--------------------*/
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

/*--------------------------------Register-------------------*/
    function registerPage(){
        return view("register");
    }

    function submitRegister(Request $request){
        $IncomingFields = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone_nb' => 'required|string|unique:users',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string',
            'role' => 'nullable|in:client,admin',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);  
        $IncomingFields['password'] = Hash::make($IncomingFields['password']);
        
        if ($request->hasFile(key: 'profile_image')) {
            $filename = time().'_'.$request->file('profile_image')->getClientOriginalName();
            $path = $request->file('profile_image')->storeAs('profile_images', $filename, 'public');
            $IncomingFields['profile_image'] = $path;
        }
      
        $user = User::create([
            'first_name' => $IncomingFields['first_name'],
            'last_name' => $IncomingFields['last_name'],
            'email' => $IncomingFields['email'],
            'password' => $IncomingFields['password'],
            'phone_nb' => $IncomingFields['phone_nb'],
            'date_of_birth' => $IncomingFields['date_of_birth'] ?? null,
            'address' => $IncomingFields['address'] ?? null,
            'role' => $IncomingFields['role'] ?? 'client',
            'profile_image' => $IncomingFields['profile_image'] ?? null,
        ]);

        // Log activity
        if (class_exists(\App\Models\Activity::class)) {
            \App\Models\Activity::create([
                'type' => 'user_registered',
                'description' => "New user '{$user->name}' registered",
                'subject_type' => \App\Models\User::class,
                'subject_id' => $user->id,
                'user_id' => $user->id,
                'properties' => ['user_id' => $user->id, 'user_name' => $user->name, 'user_email' => $user->email, 'role' => $user->role],
            ]);
        }

        Auth::login($user);
        return redirect()->route('home');
    }
}
