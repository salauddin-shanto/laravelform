<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entry;

class EntryController extends Controller
{
    public function index()
    {
        return view('entries.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        Entry::create($validated);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $entry = Entry::findOrFail($id);
        return response()->json($entry);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $entry = Entry::findOrFail($id);
        $entry->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $entry = Entry::findOrFail($id);
        $entry->delete();

        return response()->json(['success' => true]);
    }

    public function getEntries()
    {
        $entries = Entry::all();
        return response()->json(['data' => $entries]);
    }
}
