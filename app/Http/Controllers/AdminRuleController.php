<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;

class AdminRuleController extends Controller
{
    public function index()
    {
        $rules = Rule::query()->orderBy('name')->paginate(25);

        return view('admin.rules.index', compact('rules'));
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
