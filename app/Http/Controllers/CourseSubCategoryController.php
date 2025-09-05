<?php

namespace App\Http\Controllers;

use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subCategories = CourseSubCategory::with('courseCategory')
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('name')
            ->get();
        
        return view('course-sub-categories.index', compact('subCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CourseCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('course-sub-categories.form', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_category_id' => 'required|exists:course_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $subCategory = CourseSubCategory::create([
                'organization_id' => auth()->user()->organization_id,
                'course_category_id' => $request->course_category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('course-sub-categories.index')
                ->with('success', 'Course sub-category created successfully');
        } catch (\Exception $e) {
            return redirect()->route('course-sub-categories.index')
                ->with('error', 'Error creating course sub-category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseSubCategory $courseSubCategory)
    {
        return view('course-sub-categories.show', compact('courseSubCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseSubCategory $courseSubCategory)
    {
        $categories = CourseCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('course-sub-categories.form', compact('courseSubCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseSubCategory $courseSubCategory)
    {
        $request->validate([
            'course_category_id' => 'required|exists:course_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $courseSubCategory->update([
                'course_category_id' => $request->course_category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('course-sub-categories.index')
                ->with('success', 'Course sub-category updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('course-sub-categories.index')
                ->with('error', 'Error updating course sub-category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSubCategory $courseSubCategory)
    {
        try {
            // Check if sub-category has courses
            if ($courseSubCategory->courses()->count() > 0) {
                return redirect()->route('course-sub-categories.index')
                    ->with('error', 'Cannot delete sub-category that has courses');
            }

            $courseSubCategory->delete();
            return redirect()->route('course-sub-categories.index')
                ->with('success', 'Course sub-category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('course-sub-categories.index')
                ->with('error', 'Error deleting course sub-category: ' . $e->getMessage());
        }
    }
}
