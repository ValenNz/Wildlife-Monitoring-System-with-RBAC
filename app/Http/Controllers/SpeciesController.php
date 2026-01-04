<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk logging aktivitas

class SpeciesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data dengan paginasi, urutkan berdasarkan nama umum
        $species = Species::orderBy('common_name')->paginate(10);

        return view('species.index', compact('species'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya admin atau pengguna dengan hak khusus yang bisa mengakses ini
        if (!Auth::user()->can('create', Species::class)) {
            abort(403, 'You do not have permission to create species.');
        }

        return view('species.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'common_name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
            'conservation_status' => 'required|in:Critically Endangered,Endangered,Vulnerable,Least Concern,Data Deficient,Not Evaluated',
        ]);

        $species = Species::create($validated);

        // Log aktivitas: Pengguna menambahkan spesies baru
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['species_id' => $species->id])
            ->log('Created new species: ' . $species->common_name);

        return redirect()->route('species.index')->with('success', 'Species created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Species $species)
    {
        // Pastikan user memiliki izin untuk melihat detail spesies ini
        if (!Auth::user()->can('view', $species)) {
            abort(403, 'You do not have permission to view this species.');
        }

        return view('species.show', compact('species'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Species $species)
    {
        // Hanya admin atau pengguna dengan hak update yang bisa mengedit
        if (!Auth::user()->can('update', $species)) {
            abort(403, 'You do not have permission to edit this species.');
        }

        return view('species.edit', compact('species'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Species $species)
    {
        $validated = $request->validate([
            'common_name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
            'conservation_status' => 'required|in:Critically Endangered,Endangered,Vulnerable,Least Concern,Data Deficient,Not Evaluated',
        ]);

        $oldStatus = $species->conservation_status;
        $species->update($validated);

        // Log aktivitas: Pengguna memperbarui spesies
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['species_id' => $species->id, 'old_status' => $oldStatus, 'new_status' => $species->conservation_status])
            ->log('Updated species: ' . $species->common_name);

        return redirect()->route('species.index')->with('success', 'Species updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Species $species)
    {
        // Hanya admin atau pengguna dengan hak delete yang bisa menghapus
        if (!Auth::user()->can('delete', $species)) {
            abort(403, 'You do not have permission to delete this species.');
        }

        $speciesName = $species->common_name;
        $species->delete();

        // Log aktivitas: Pengguna menghapus spesies
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['species_id' => $species->id])
            ->log('Deleted species: ' . $speciesName);

        return redirect()->route('species.index')->with('success', 'Species deleted successfully.');
    }

    /**
     * Export species data.
     */
    public function export()
    {
        // Implementasi ekspor CSV/GeoJSON akan ditambahkan di sini
        // Berdasarkan FR-07 dalam SRS
        return response()->json(['message' => 'Export functionality is under development.']);
    }
}
