<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with(['courseCategory', 'courseSubCategory', 'students'])
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('courses.index', compact('courses'));
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
        
        $subCategories = CourseSubCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('courses.form', compact('categories', 'subCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:50',
            'course_category_id' => 'nullable|exists:course_categories,id',
            'course_sub_category_id' => 'nullable|exists:course_sub_categories,id',
            'tags' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_archived' => 'boolean'
        ]);

        try {
            $data = [
                'organization_id' => auth()->user()->organization_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'duration' => $request->duration,
                'level' => $request->level,
                'language' => $request->language,
                'course_category_id' => $request->course_category_id,
                'course_sub_category_id' => $request->course_sub_category_id,
                'tags' => $request->tags,
                'keywords' => $request->keywords,
                'price' => $request->price ?? 0,
                'is_published' => $request->has('is_published'),
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'is_archived' => $request->has('is_archived'),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $folder = auth()->user()->organization_id . '/course_images';
                if (!Storage::disk('public')->exists($folder)) {
                    Storage::disk('public')->makeDirectory($folder);
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->storeAs($folder, $imageName, 'public');
                $data['image'] = $imageName;
            }

            $course = Course::create($data);

            return redirect()->route('courses.index')
                ->with('success', 'Course created successfully');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Error creating course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $categories = CourseCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $subCategories = CourseSubCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('courses.form', compact('course', 'categories', 'subCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:50',
            'course_category_id' => 'nullable|exists:course_categories,id',
            'course_sub_category_id' => 'nullable|exists:course_sub_categories,id',
            'tags' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_archived' => 'boolean'
        ]);

        try {
            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'duration' => $request->duration,
                'level' => $request->level,
                'language' => $request->language,
                'course_category_id' => $request->course_category_id,
                'course_sub_category_id' => $request->course_sub_category_id,
                'tags' => $request->tags,
                'keywords' => $request->keywords,
                'price' => $request->price ?? 0,
                'is_published' => $request->has('is_published'),
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'is_archived' => $request->has('is_archived'),
                'updated_by' => auth()->user()->id,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $folder = auth()->user()->organization_id . '/course_images';
                if (!Storage::disk('root_public')->exists($folder)) {
                    Storage::disk('root_public')->makeDirectory($folder);
                }
                
                // Delete old image if exists
                if ($course->image && Storage::disk('root_public')->exists($folder . '/' . $course->image)) {
                    Storage::disk('root_public')->delete($folder . '/' . $course->image);
                }
                
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->storeAs($folder, $imageName, 'root_public');
                $data['image'] = $imageName;
            }

            $course->update($data);

            return redirect()->route('courses.index')
                ->with('success', 'Course updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Error updating course: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        try {
            // Check if course has enrolled students
            if ($course->students()->count() > 0) {
                return redirect()->route('courses.index')
                    ->with('error', 'Cannot delete course that has enrolled students');
            }

            // Delete course image if exists
            if ($course->image) {
                $folder = auth()->user()->organization_id . '/course_images';
                if (Storage::disk('root_public')->exists($folder . '/' . $course->image)) {
                    Storage::disk('root_public')->delete($folder . '/' . $course->image);
                }
            }

            $course->delete();
            return redirect()->route('courses.index')
                ->with('success', 'Course deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }
}
