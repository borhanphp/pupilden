<?php

namespace App\Http\Controllers;

use App\Models\OrganizationTheme;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = OrganizationTheme::with(['organization', 'theme'])
                ->where('organization_id', auth()->user()->organization_id);

            // Search by theme name
            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('theme', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $organizationThemes = $query->orderBy('created_at', 'desc')->get();

            return view('organization-themes.index', compact('organizationThemes'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving organization themes: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $themes = Theme::where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('organization-themes.form', compact('themes'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'theme_id' => 'required|exists:themes,id',
                'custom_settings' => 'nullable|json',
            ]);

            // Check if organization already has a theme
            $existingTheme = OrganizationTheme::where('organization_id', auth()->user()->organization_id)
                ->first();

            if ($existingTheme) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Your organization already has a theme assigned. Please update the existing theme instead.');
            }

            $customSettings = null;
            if ($request->custom_settings) {
                $decoded = json_decode($request->custom_settings, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $customSettings = $decoded;
                }
            }

            $organizationTheme = OrganizationTheme::create([
                'organization_id' => auth()->user()->organization_id,
                'theme_id' => $request->theme_id,
                'custom_settings' => $customSettings,
            ]);

            return redirect()->route('organization-themes.index')
                ->with('success', 'Organization theme created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating organization theme: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrganizationTheme $organizationTheme)
    {
        try {
            // Verify the organization theme belongs to the logged-in user's organization
            if ($organizationTheme->organization_id !== auth()->user()->organization_id) {
                return redirect()->route('organization-themes.index')
                    ->with('error', 'Unauthorized access.');
            }

            $organizationTheme->load(['organization', 'theme']);

            return view('organization-themes.show', compact('organizationTheme'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving organization theme: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrganizationTheme $organizationTheme)
    {
        try {
            // Verify the organization theme belongs to the logged-in user's organization
            if ($organizationTheme->organization_id !== auth()->user()->organization_id) {
                return redirect()->route('organization-themes.index')
                    ->with('error', 'Unauthorized access.');
            }

            $themes = Theme::where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('organization-themes.form', compact('organizationTheme', 'themes'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrganizationTheme $organizationTheme)
    {
        try {
            // Verify the organization theme belongs to the logged-in user's organization
            if ($organizationTheme->organization_id !== auth()->user()->organization_id) {
                return redirect()->route('organization-themes.index')
                    ->with('error', 'Unauthorized access.');
            }

            $request->validate([
                'theme_id' => 'required|exists:themes,id',
                'custom_settings' => 'nullable|json',
            ]);

            $customSettings = null;
            if ($request->custom_settings) {
                $decoded = json_decode($request->custom_settings, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $customSettings = $decoded;
                }
            }

            $organizationTheme->update([
                'theme_id' => $request->theme_id,
                'custom_settings' => $customSettings,
            ]);

            return redirect()->route('organization-themes.index')
                ->with('success', 'Organization theme updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating organization theme: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrganizationTheme $organizationTheme)
    {
        try {
            // Verify the organization theme belongs to the logged-in user's organization
            if ($organizationTheme->organization_id !== auth()->user()->organization_id) {
                return redirect()->route('organization-themes.index')
                    ->with('error', 'Unauthorized access.');
            }

            $organizationTheme->delete();

            return redirect()->route('organization-themes.index')
                ->with('success', 'Organization theme deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting organization theme: ' . $e->getMessage());
        }
    }
}
