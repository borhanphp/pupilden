<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=Domain::where('organization_id', auth()->user()->organization_id)->get();
        return view('domains.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('domains.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $data=Domain::create([
                'organization_id' => auth()->user()->organization_id,
                'domain_name' => $request->name,
                'is_primary' => $request->is_primary,
                'is_active' => true,
                'is_verified' => false,
                'is_expired' => false,
                'activation_date' => null,
                'expiry_date' => null,
                'created_by' => auth()->user()->id,
            ]);
            return redirect()->route('domains.index')->with('success', 'Domain created successfully');
        } catch (\Exception $e) {
            return redirect()->route('domains.index')->with('error', 'Error creating domain: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain)
    {
        return view('domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Domain $domain)
    {
        return view('domains.form', compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Domain $domain)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $domain->update([
            'domain_name' => $request->name,
            'is_primary' => $request->has('is_primary'),
            'updated_by' => auth()->user()->id,
        ]);
        
        return redirect()->route('domains.index')->with('success', 'Domain updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain)
    {
        $domain->delete();
        return redirect()->route('domains.index')->with('success', 'Domain deleted successfully');
    }
}
