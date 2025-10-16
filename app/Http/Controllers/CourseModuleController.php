<?php

namespace App\Http\Controllers;

use App\Models\CourseModule;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseModule::with(['course', 'files']);

        // Filter by course if course_id is provided
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courseModules = $query->orderBy('order')->paginate(15);
        $courses = Course::where('is_active', true)->get();

        return view('course-modules.index', compact('courseModules', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $courses = Course::where('is_active', true)->get();
        $selectedCourseId = $request->get('course_id');
        
        return view('course-modules.create', compact('courses', 'selectedCourseId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'order' => 'nullable|integer|min:0',
            'duration' => 'nullable|integer|min:0',
            'duration_type' => 'nullable|in:minutes,hours,days',
            'duration_value' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('course-modules', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        // Set default order if not provided
        if (!isset($data['order'])) {
            $maxOrder = CourseModule::where('course_id', $data['course_id'])->max('order');
            $data['order'] = ($maxOrder ?? 0) + 1;
        }

        CourseModule::create($data);

        return redirect()->route('course-modules.index')
            ->with('success', 'Course module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseModule $courseModule)
    {
        $courseModule->load(['course', 'files']);
        
        return view('course-modules.show', compact('courseModule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseModule $courseModule)
    {
        $courses = Course::where('is_active', true)->get();
        
        return view('course-modules.edit', compact('courseModule', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseModule $courseModule)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'order' => 'nullable|integer|min:0',
            'duration' => 'nullable|integer|min:0',
            'duration_type' => 'nullable|in:minutes,hours,days',
            'duration_value' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($courseModule->image && Storage::disk('public')->exists($courseModule->image)) {
                Storage::disk('public')->delete($courseModule->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('course-modules', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $courseModule->update($data);

        return redirect()->route('course-modules.index')
            ->with('success', 'Course module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseModule $courseModule)
    {
        // Delete associated image if exists
        if ($courseModule->image && Storage::disk('public')->exists($courseModule->image)) {
            Storage::disk('public')->delete($courseModule->image);
        }

        $courseModule->delete();

        return redirect()->route('course-modules.index')
            ->with('success', 'Course module deleted successfully.');
    }

    /**
     * Update the order of course modules
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:course_modules,id',
            'modules.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->modules as $module) {
            CourseModule::where('id', $module['id'])
                ->update(['order' => $module['order'], 'updated_by' => Auth::id()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle the status of a course module
     */
    public function toggleStatus(CourseModule $courseModule)
    {
        $courseModule->update([
            'status' => $courseModule->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Course module status updated successfully.');
    }
}
