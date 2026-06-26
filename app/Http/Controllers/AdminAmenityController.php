<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAmenityController extends Controller
{
    public function index(Request $request)
    {
        $search = Str::limit(trim((string) $request->query('search', '')), 255);

        $amenities = Amenity::query()
            ->when($search !== '', static function ($query) use ($search) {
                $likeTerm = '%' . addcslashes($search, '\\%_') . '%';
                $query->where('name', 'like', $likeTerm);
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.amenities.index', compact('amenities', 'search'));
    }

    public function create()
    {
        return view('admin.amenities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:amenities,name'],
        ]);

        Amenity::create($validated);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity created.');
    }

    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:amenities,name,'.$amenity->id],
        ]);

        $amenity->update($validated);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity updated.');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity removed. It is no longer attached to listings.');
    }
}
