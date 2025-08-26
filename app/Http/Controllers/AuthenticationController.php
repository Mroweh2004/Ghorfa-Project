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
            'name'=> 'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8|confirmed',
            'phone_nb' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);  
        $IncomingFields['password'] =  Hash::make($IncomingFields['password']);
        
        if ($request->hasFile(key: 'profile_image')) {
            $filename = time().'_'.$request->file('profile_image')->getClientOriginalName();
            $path = $request->file('profile_image')->storeAs('profile_images', $filename, 'public');
            $IncomingFields['profile_image'] = $path;
        }
      
        $user = User::create( [
            'name' => $IncomingFields['name'],
            'email' => $IncomingFields['email'],
            'password' => $IncomingFields['password'],
            'phone_nb' => $IncomingFields['phone_nb'],
            'profile_image' => $IncomingFields['profile_image'] ?? null,
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }
}
