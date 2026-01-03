<?php
namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;


class SpeciesController extends Controller
{
    public function index()
    {
        $species = Species::paginate(10);
        return view('species.index', compact('species'));
    }

    public function create()
    {
        return view('species.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
        ]);

        Species::create($request->all());
        return redirect()->route('species.index')->with('success', 'Species created successfully.');
    }

    public function show(Species $species)
    {
        return view('species.show', compact('species'));
    }

    public function edit(Species $species)
    {
        return view('species.edit', compact('species'));
    }

    public function update(Request $request, Species $species)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
        ]);

        $species->update($request->all());
        return redirect()->route('species.index')->with('success', 'Species updated successfully.');
    }

    public function destroy(Species $species)
    {
        $species->delete();
        return redirect()->route('species.index')->with('success', 'Species deleted successfully.');
    }

    public function export()
    {
        // Logic for exporting species data
    }
}
