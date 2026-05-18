<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeacherCourseController extends Controller
{
    private function resolveOrgId(Request $request, $teacher): int|null
    {
        $orgId = $request->header('X-Organization-Id') ?: $request->input('organization_id');
        if (!$orgId) return null;

        // Verify teacher belongs to this org
        $belongs = $teacher->activeOrganizations()->where('organizations.id', $orgId)->exists();
        return $belongs ? (int) $orgId : null;
    }

    public function index(Request $request)
    {
        $teacher = $request->user();
        $orgIds  = $this->getAllOrgIds($teacher, $request);

        $courses = Course::with(['courseCategory:id,name', 'organization:id,name'])
            ->where('teacher_id', $teacher->id)
            ->whereIn('organization_id', $orgIds)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status === 'published',  fn($q) => $q->where('is_published', true)->where('is_active', true))
            ->when($request->status === 'draft',      fn($q) => $q->where('is_published', false))
            ->when($request->status === 'archived',   fn($q) => $q->where('is_archived', true))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $courses->through(fn($c) => $this->formatCourse($c)),
        ]);
    }

    public function store(Request $request)
    {
        $teacher = $request->user();
        $orgId   = $this->resolveOrgId($request, $teacher);

        if (!$orgId) {
            return response()->json(['success' => false, 'message' => 'Valid organization required'], 422);
        }

        $validator = Validator::make($request->all(), [
            'name'                   => 'required|string|max:255',
            'description'            => 'nullable|string',
            'price'                  => 'required|numeric|min:0',
            'level'                  => 'nullable|in:beginner,intermediate,advanced',
            'language'               => 'nullable|string|max:50',
            'course_category_id'     => 'nullable|exists:course_categories,id',
            'course_sub_category_id' => 'nullable|exists:course_sub_categories,id',
            'tags'                   => 'nullable|string',
            'image'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published'           => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'price', 'level', 'language',
                                 'course_category_id', 'course_sub_category_id', 'tags']);
        $data['organization_id'] = $orgId;
        $data['teacher_id']      = $teacher->id;
        $data['is_published']    = $request->boolean('is_published', false);
        $data['is_active']       = true;
        $data['slug']            = $this->uniqueSlug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store("{$orgId}/courses", 'r2');
        }

        $course = Course::create($data);

        return response()->json(['success' => true, 'message' => 'Course created', 'data' => $this->formatCourse($course)], 201);
    }

    public function show(Request $request, $id)
    {
        $course = $this->findOwnedCourse($request, $id);
        if (!$course) return response()->json(['success' => false, 'message' => 'Course not found'], 404);

        $course->load(['courseCategory', 'organization', 'modules.videos']);
        return response()->json(['success' => true, 'data' => $this->formatCourse($course)]);
    }

    public function update(Request $request, $id)
    {
        $teacher = $request->user();
        $course  = $this->findOwnedCourse($request, $id);
        if (!$course) return response()->json(['success' => false, 'message' => 'Course not found'], 404);

        $validator = Validator::make($request->all(), [
            'name'                   => 'sometimes|string|max:255',
            'description'            => 'nullable|string',
            'price'                  => 'sometimes|numeric|min:0',
            'level'                  => 'nullable|in:beginner,intermediate,advanced',
            'language'               => 'nullable|string|max:50',
            'course_category_id'     => 'nullable|exists:course_categories,id',
            'course_sub_category_id' => 'nullable|exists:course_sub_categories,id',
            'tags'                   => 'nullable|string',
            'image'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published'           => 'boolean',
            'is_archived'            => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'price', 'level', 'language',
                                 'course_category_id', 'course_sub_category_id', 'tags']);

        if ($request->has('is_published')) $data['is_published'] = $request->boolean('is_published');
        if ($request->has('is_archived'))  $data['is_archived']  = $request->boolean('is_archived');

        if ($request->hasFile('image')) {
            if ($course->image) Storage::disk('r2')->delete($course->image);
            $data['image'] = $request->file('image')->store("{$course->organization_id}/courses", 'r2');
        }

        if ($request->filled('name') && $request->name !== $course->name) {
            $data['slug'] = $this->uniqueSlug($request->name, $course->id);
        }

        $course->update($data);

        return response()->json(['success' => true, 'message' => 'Course updated', 'data' => $this->formatCourse($course->fresh())]);
    }

    public function destroy(Request $request, $id)
    {
        $course = $this->findOwnedCourse($request, $id);
        if (!$course) return response()->json(['success' => false, 'message' => 'Course not found'], 404);

        if ($course->image) Storage::disk('r2')->delete($course->image);
        $course->delete();

        return response()->json(['success' => true, 'message' => 'Course deleted']);
    }

    public function categories(Request $request)
    {
        $categories = CourseCategory::with('subCategories:id,course_category_id,name')
                                    ->select('id', 'name')
                                    ->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    // --- Helpers ---

    private function findOwnedCourse(Request $request, $id)
    {
        $teacher = $request->user();
        $orgIds  = $this->getAllOrgIds($teacher, $request);
        return Course::where('id', $id)
                     ->where('teacher_id', $teacher->id)
                     ->whereIn('organization_id', $orgIds)
                     ->first();
    }

    private function getAllOrgIds($teacher, Request $request): array
    {
        $orgId = $request->header('X-Organization-Id') ?: $request->query('org_id');
        if ($orgId) {
            $belongs = $teacher->activeOrganizations()->where('organizations.id', $orgId)->exists();
            return $belongs ? [(int) $orgId] : [];
        }
        return $teacher->activeOrganizations()->pluck('organizations.id')->toArray();
    }

    private function uniqueSlug(string $name, $excludeId = null): string
    {
        $slug  = Str::slug($name);
        $base  = $slug;
        $count = 1;
        while (Course::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }
        return $slug;
    }

    private function formatCourse(Course $course): array
    {
        return [
            'id'            => $course->id,
            'name'          => $course->name,
            'slug'          => $course->slug,
            'description'   => $course->description,
            'price'         => $course->price,
            'level'         => $course->level,
            'language'      => $course->language,
            'tags'          => $course->tags,
            'is_published'  => $course->is_published,
            'is_archived'   => $course->is_archived,
            'image_url'     => $course->image ? Storage::disk('r2')->url($course->image) : null,
            'category'      => $course->relationLoaded('courseCategory') ? $course->courseCategory?->name : null,
            'organization'  => $course->relationLoaded('organization')   ? $course->organization?->name  : null,
            'organization_id' => $course->organization_id,
            'modules_count' => $course->relationLoaded('modules') ? $course->modules->count() : null,
            'created_at'    => $course->created_at?->toDateString(),
        ];
    }
}
