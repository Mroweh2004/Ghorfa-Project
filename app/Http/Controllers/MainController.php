<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Listing;
use App\Models\Property;

class MainController extends Controller
{
    function homePage(){
        return view("home");
    }

    function profilePage(){
        $properties = Property::where('user_id', auth()->id())->paginate(12);
        return view("profile", compact('properties'));
    }

    function searchPage(){
        $properties = Property::paginate(12);
        return view("search", compact('properties'));
    }

    function propertyPage(){
        return view("list-property");
    }
}
    
  
   

