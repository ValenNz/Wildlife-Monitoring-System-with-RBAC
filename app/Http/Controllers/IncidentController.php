<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidentController extends Controller
{
    /**
     * Display a listing of incidents with filters
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->get('search', '');
        $severity = $request->get('severity', '');
        $status = $request->get('status', '');
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $perPage = $request->get('per_page', 20);

        // Base query
        $query = DB::table('incidents as i')
            ->leftJoin('animals as a', 'i.animal_id', '=', 'a.id')
            ->leftJoin('users as u', 'i.assigned_to', '=', 'u.id')
            ->select(
                'i.*',
                'a.name as animal_name',
                'a.species',
                'u.name as assigned_user'
            );

        // Apply search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('i.title', 'LIKE', "%{$search}%")
                  ->orWhere('i.description', 'LIKE', "%{$search}%")
                  ->orWhere('a.name', 'LIKE', "%{$search}%");
            });
        }

        // Apply filters
        if (!empty($severity)) {
            $query->where('i.severity', $severity);
        }

        if (!empty($status)) {
            $query->where('i.status', $status);
        }

        // Apply sorting
        $allowedSorts = ['created_at', 'title', 'severity', 'status', 'resolved_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        $query->orderBy('i.' . $sortBy, $sortOrder);

        // Paginate
        $incidents = $query->paginate($perPage)
            ->appends([
                'search' => $search,
                'severity' => $severity,
                'status' => $status,
                'sort' => $sortBy,
                'order' => $sortOrder,
                'per_page' => $perPage
            ]);

        // Statistics
        $totalIncidents = DB::table('incidents')->count();
        $openIncidents = DB::table('incidents')->where('status', 'open')->count();
        $criticalIncidents = DB::table('incidents')->where('severity', 'critical')->count();
        $resolvedToday = DB::table('incidents')
            ->where('resolved_at', '>=', Carbon::today())
            ->count();

        return view('incidents.index', compact(
            'incidents',
            'totalIncidents',
            'openIncidents',
            'criticalIncidents',
            'resolvedToday',
            'search',
            'severity',
            'status',
            'sortBy',
            'sortOrder',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new incident
     */
    public function create()
    {
        // Get animals for dropdown
        $animals = DB::table('animals')
            ->select('id', 'name', 'species', 'tag_id')
            ->orderBy('name')
            ->get();

        // Get users for assignment
        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('incidents.create', compact('animals', 'users'));
    }

    /**
     * Store a newly created incident
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,investigating,resolved,closed',
            'animal_id' => 'nullable|exists:animals,id',
            'assigned_to' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $id = DB::table('incidents')->insertGetId([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
                'status' => $validated['status'],
                'animal_id' => $validated['animal_id'] ?? null,
                'assigned_to' => $validated['assigned_to'] ?? null,
                'location' => $validated['location'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'reported_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()
                ->route('incidents.show', $id)
                ->with('success', 'Incident created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create incident: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified incident
     */
    public function show($id)
    {
        $incident = DB::table('incidents as i')
            ->leftJoin('animals as a', 'i.animal_id', '=', 'a.id')
            ->leftJoin('users as u', 'i.assigned_to', '=', 'u.id')
            ->leftJoin('users as r', 'i.resolved_by', '=', 'r.id')
            ->where('i.id', $id)
            ->select(
                'i.*',
                'a.name as animal_name',
                'a.species',
                'a.tag_id',
                'u.name as assigned_user',
                'u.email as assigned_email',
                'r.name as resolved_by_name'
            )
            ->first();

        if (!$incident) {
            abort(404, 'Incident not found');
        }

        // Get all users for assignment
        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('incidents.show', compact('incident', 'users'));
    }

    /**
     * Show the form for editing the specified incident
     */
    public function edit($id)
    {
        $incident = DB::table('incidents')
            ->where('id', $id)
            ->first();

        if (!$incident) {
            abort(404, 'Incident not found');
        }

        // Get animals for dropdown
        $animals = DB::table('animals')
            ->select('id', 'name', 'species', 'tag_id')
            ->orderBy('name')
            ->get();

        // Get users for assignment
        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('incidents.edit', compact('incident', 'animals', 'users'));
    }

    /**
     * Update the specified incident
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,investigating,resolved,closed',
            'animal_id' => 'nullable|exists:animals,id',
            'assigned_to' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $updated = DB::table('incidents')
                ->where('id', $id)
                ->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'severity' => $validated['severity'],
                    'status' => $validated['status'],
                    'animal_id' => $validated['animal_id'] ?? null,
                    'assigned_to' => $validated['assigned_to'] ?? null,
                    'location' => $validated['location'] ?? null,
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'updated_at' => Carbon::now(),
                ]);

            if ($updated) {
                return redirect()
                    ->route('incidents.show', $id)
                    ->with('success', 'Incident updated successfully!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Incident not found or no changes made.');
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update incident: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified incident
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('incidents')
                ->where('id', $id)
                ->delete();

            if ($deleted) {
                return redirect()
                    ->route('incidents.index')
                    ->with('success', 'Incident deleted successfully!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Incident not found.');
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete incident: ' . $e->getMessage());
        }
    }

    /**
     * Resolve an incident
     */
    public function resolve(Request $request, $id)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        try {
            // Get current user (in real app, use auth()->id())
            $userId = DB::table('users')->first()->id ?? null;

            $updated = DB::table('incidents')
                ->where('id', $id)
                ->update([
                    'status' => 'resolved',
                    'resolution_notes' => $validated['resolution_notes'],
                    'resolved_at' => Carbon::now(),
                    'resolved_by' => $userId,
                    'updated_at' => Carbon::now(),
                ]);

            if ($updated) {
                return redirect()
                    ->route('incidents.show', $id)
                    ->with('success', 'Incident resolved successfully!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Incident not found.');
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to resolve incident: ' . $e->getMessage());
        }
    }

    /**
     * Escalate an incident
     */
    public function escalate(Request $request, $id)
    {
        try {
            // Increase severity level
            $incident = DB::table('incidents')->where('id', $id)->first();

            if (!$incident) {
                return redirect()->back()->with('error', 'Incident not found.');
            }

            $newSeverity = match($incident->severity) {
                'low' => 'medium',
                'medium' => 'high',
                'high' => 'critical',
                'critical' => 'critical', // Already at max
            };

            DB::table('incidents')
                ->where('id', $id)
                ->update([
                    'severity' => $newSeverity,
                    'status' => 'investigating',
                    'updated_at' => Carbon::now(),
                ]);

            return redirect()
                ->route('incidents.show', $id)
                ->with('success', 'Incident escalated to ' . $newSeverity . ' severity!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to escalate incident: ' . $e->getMessage());
        }
    }

    /**
     * Assign incident to user
     */
    public function assign(Request $request, $id)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        try {
            $updated = DB::table('incidents')
                ->where('id', $id)
                ->update([
                    'assigned_to' => $validated['assigned_to'],
                    'status' => 'investigating',
                    'updated_at' => Carbon::now(),
                ]);

            if ($updated) {
                return redirect()
                    ->route('incidents.show', $id)
                    ->with('success', 'Incident assigned successfully!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Incident not found.');
            }

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to assign incident: ' . $e->getMessage());
        }
    }

    /**
     * Export incidents to JSON
     */
    public function export(Request $request)
    {
        $incidents = DB::table('incidents as i')
            ->leftJoin('animals as a', 'i.animal_id', '=', 'a.id')
            ->leftJoin('users as u', 'i.assigned_to', '=', 'u.id')
            ->select(
                'i.*',
                'a.name as animal_name',
                'a.species',
                'u.name as assigned_user'
            )
            ->orderBy('i.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $incidents->count(),
            'exported_at' => Carbon::now()->toDateTimeString(),
            'data' => $incidents
        ]);
    }
}
