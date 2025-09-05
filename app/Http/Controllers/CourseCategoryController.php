<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CourseCategory::where('organization_id', auth()->user()->organization_id)
            ->orderBy('name')
            ->get();
        
        return view('course-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('course-categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        try {
            $category = CourseCategory::create([
                'organization_id' => auth()->user()->organization_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('course-categories.index')
                ->with('success', 'Course category created successfully');
        } catch (\Exception $e) {
            return redirect()->route('course-categories.index')
                ->with('error', 'Error creating course category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseCategory $courseCategory)
    {
        return view('course-categories.show', compact('courseCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseCategory $courseCategory)
    {
        return view('course-categories.form', compact('courseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseCategory $courseCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        try {
            $courseCategory->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('course-categories.index')
                ->with('success', 'Course category updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('course-categories.index')
                ->with('error', 'Error updating course category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCategory $courseCategory)
    {
        try {
            // Check if category has courses
            if ($courseCategory->courses()->count() > 0) {
                return redirect()->route('course-categories.index')
                    ->with('error', 'Cannot delete category that has courses');
            }

            $courseCategory->delete();
            return redirect()->route('course-categories.index')
                ->with('success', 'Course category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('course-categories.index')
                ->with('error', 'Error deleting course category: ' . $e->getMessage());
        }
    }
}
