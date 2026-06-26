<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRuleController extends Controller
{
    public function index(Request $request)
    {
        $search = Str::limit(trim((string) $request->query('search', '')), 255);

        $rules = Rule::query()
            ->when($search !== '', static function ($query) use ($search) {
                $likeTerm = '%' . addcslashes($search, '\\%_') . '%';
                $query->where('name', 'like', $likeTerm);
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.rules.index', compact('rules', 'search'));
    }

    public function create()
    {
        return view('admin.rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:rules,name'],
        ]);

        Rule::create($validated);

        return redirect()->route('admin.rules.index')
            ->with('success', 'Rule created.');
    }

    public function edit(Rule $rule)
    {
        return view('admin.rules.edit', compact('rule'));
    }

    public function update(Request $request, Rule $rule)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:rules,name,'.$rule->id],
        ]);

        $rule->update($validated);

        return redirect()->route('admin.rules.index')
            ->with('success', 'Rule updated.');
    }

    public function destroy(Rule $rule)
    {
        $rule->delete();

        return redirect()->route('admin.rules.index')
            ->with('success', 'Rule removed. It is no longer attached to listings.');
    }
}
