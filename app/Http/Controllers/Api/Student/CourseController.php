<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\BaseController;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends BaseController
{
    /**
     * Get all courses for the student's organization
     */
    public function index(Request $request)
    {
        try {
            $student = $request->user('student');
            
            $query = Course::with(['courseCategory', 'courseSubCategory', 'students'])
                ->where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false);

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('course_category_id', $request->category_id);
            }

            // Filter by sub-category
            if ($request->has('sub_category_id')) {
                $query->where('course_sub_category_id', $request->sub_category_id);
            }

            // Filter by level
            if ($request->has('level')) {
                $query->where('level', $request->level);
            }

            // Filter by price (free/paid)
            if ($request->has('price_type')) {
                if ($request->price_type === 'free') {
                    $query->where('price', 0);
                } elseif ($request->price_type === 'paid') {
                    $query->where('price', '>', 0);
                }
            }

            // Search by name or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
                });
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'price', 'created_at', 'level'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $courses = $query->paginate($perPage);

            // Transform the data
            $courses->getCollection()->transform(function ($course) use ($student) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'duration' => $course->duration,
                    'level' => $course->level,
                    'language' => $course->language,
                    'price' => $course->price,
                    'image_url' => $course->image ? Storage::disk('r2')->url($student->organization_id . '/course_images/' . $course->image) : null,
                    'category' => $course->courseCategory ? [
                        'id' => $course->courseCategory->id,
                        'name' => $course->courseCategory->name,
                        'slug' => $course->courseCategory->slug
                    ] : null,
                    'sub_category' => $course->courseSubCategory ? [
                        'id' => $course->courseSubCategory->id,
                        'name' => $course->courseSubCategory->name,
                        'slug' => $course->courseSubCategory->slug
                    ] : null,
                    'tags' => $course->tags,
                    'is_featured' => $course->is_featured,
                    'enrolled_students_count' => $course->students->count(),
                    'is_enrolled' => $course->students->contains($student->id),
                    'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $course->updated_at->format('Y-m-d H:i:s')
                ];
            });

            return $this->success('Courses retrieved successfully', [
                'courses' => $courses->items(),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'from' => $courses->firstItem(),
                    'to' => $courses->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return $this->error('Error retrieving courses', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get featured courses
     */
    public function featured(Request $request)
    {
        try {
            $student = $request->user('student');
            
            $courses = Course::with(['courseCategory', 'courseSubCategory', 'students'])
                ->where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $courses->transform(function ($course) use ($student) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'duration' => $course->duration,
                    'level' => $course->level,
                    'language' => $course->language,
                    'price' => $course->price,
                    'image_url' => $course->image ? Storage::disk('r2')->url($student->organization_id . '/course_images/' . $course->image) : null,
                    'category' => $course->courseCategory ? [
                        'id' => $course->courseCategory->id,
                        'name' => $course->courseCategory->name,
                        'slug' => $course->courseCategory->slug
                    ] : null,
                    'sub_category' => $course->courseSubCategory ? [
                        'id' => $course->courseSubCategory->id,
                        'name' => $course->courseSubCategory->name,
                        'slug' => $course->courseSubCategory->slug
                    ] : null,
                    'tags' => $course->tags,
                    'enrolled_students_count' => $course->students->count(),
                    'is_enrolled' => $course->students->contains($student->id),
                    'created_at' => $course->created_at->format('Y-m-d H:i:s')
                ];
            });

            return $this->success('Featured courses retrieved successfully', ['courses' => $courses]);

        } catch (\Exception $e) {
            return $this->error('Error retrieving featured courses', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get course details
     */
    public function show(Request $request, $id)
    {
        try {
            $student = $request->user('student');
            
            $course = Course::with(['courseCategory', 'courseSubCategory', 'students', 'creator', 'updater'])
                ->where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->findOrFail($id);

            $courseData = [
                'id' => $course->id,
                'name' => $course->name,
                'slug' => $course->slug,
                'description' => $course->description,
                'duration' => $course->duration,
                'level' => $course->level,
                'language' => $course->language,
                'price' => $course->price,
                'image_url' => $course->image ? Storage::disk('r2')->url($student->organization_id . '/course_images/' . $course->image) : null,
                'category' => $course->courseCategory ? [
                    'id' => $course->courseCategory->id,
                    'name' => $course->courseCategory->name,
                    'slug' => $course->courseCategory->slug,
                    'description' => $course->courseCategory->description
                ] : null,
                'sub_category' => $course->courseSubCategory ? [
                    'id' => $course->courseSubCategory->id,
                    'name' => $course->courseSubCategory->name,
                    'slug' => $course->courseSubCategory->slug,
                    'description' => $course->courseSubCategory->description
                ] : null,
                'tags' => $course->tags,
                'keywords' => $course->keywords,
                'is_featured' => $course->is_featured,
                'enrolled_students_count' => $course->students->count(),
                'is_enrolled' => $course->students->contains($student->id),
                'creator' => $course->creator ? [
                    'id' => $course->creator->id,
                    'name' => $course->creator->name,
                    'email' => $course->creator->email
                ] : null,
                'updater' => $course->updater ? [
                    'id' => $course->updater->id,
                    'name' => $course->updater->name,
                    'email' => $course->updater->email
                ] : null,
                'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $course->updated_at->format('Y-m-d H:i:s')
            ];

            return $this->success('Course details retrieved successfully', ['course' => $courseData]);

        } catch (\Exception $e) {
            return $this->error('Error retrieving course details', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get course categories
     */
    public function categories(Request $request)
    {
        try {
            $student = $request->user('student');
            
            $categories = CourseCategory::with(['subCategories', 'courses'])
                ->where('organization_id', $student->organization_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $categories->transform(function ($category) use ($student) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'icon' => $category->icon,
                    'courses_count' => $category->courses->where('is_published', true)->where('is_active', true)->where('is_archived', false)->count(),
                    'sub_categories' => $category->subCategories->where('is_active', true)->map(function ($subCategory) use ($student) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'slug' => $subCategory->slug,
                            'description' => $subCategory->description,
                            'courses_count' => $subCategory->courses->where('is_published', true)->where('is_active', true)->where('is_archived', false)->count()
                        ];
                    })
                ];
            });

            return $this->success('Categories retrieved successfully', ['categories' => $categories]);

        } catch (\Exception $e) {
            return $this->error('Error retrieving categories', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get courses by category
     */
    public function byCategory(Request $request, $categoryId)
    {
        try {
            $student = $request->user('student');
            
            $category = CourseCategory::where('organization_id', $student->organization_id)
                ->where('is_active', true)
                ->findOrFail($categoryId);

            $query = Course::with(['courseSubCategory', 'students'])
                ->where('organization_id', $student->organization_id)
                ->where('course_category_id', $categoryId)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false);

            // Apply filters
            if ($request->has('sub_category_id')) {
                $query->where('course_sub_category_id', $request->sub_category_id);
            }

            if ($request->has('level')) {
                $query->where('level', $request->level);
            }

            if ($request->has('price_type')) {
                if ($request->price_type === 'free') {
                    $query->where('price', 0);
                } elseif ($request->price_type === 'paid') {
                    $query->where('price', '>', 0);
                }
            }

            // Sort and paginate
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $perPage = $request->get('per_page', 10);
            
            $courses = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            $courses->getCollection()->transform(function ($course) use ($student) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'duration' => $course->duration,
                    'level' => $course->level,
                    'language' => $course->language,
                    'price' => $course->price,
                    'image_url' => $course->image ? Storage::disk('r2')->url($student->organization_id . '/course_images/' . $course->image) : null,
                    'sub_category' => $course->courseSubCategory ? [
                        'id' => $course->courseSubCategory->id,
                        'name' => $course->courseSubCategory->name,
                        'slug' => $course->courseSubCategory->slug
                    ] : null,
                    'tags' => $course->tags,
                    'is_featured' => $course->is_featured,
                    'enrolled_students_count' => $course->students->count(),
                    'is_enrolled' => $course->students->contains($student->id),
                    'created_at' => $course->created_at->format('Y-m-d H:i:s')
                ];
            });

            return $this->success('Courses by category retrieved successfully', [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'icon' => $category->icon
                ],
                'courses' => $courses->items(),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'from' => $courses->firstItem(),
                    'to' => $courses->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return $this->error('Error retrieving courses by category', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Search courses
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2'
            ]);

            $student = $request->user('student');
            $query = $request->get('query');
            
            $courses = Course::with(['courseCategory', 'courseSubCategory', 'students'])
                ->where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('tags', 'like', "%{$query}%")
                      ->orWhere('keywords', 'like', "%{$query}%");
                })
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $courses->transform(function ($course) use ($student) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'duration' => $course->duration,
                    'level' => $course->level,
                    'language' => $course->language,
                    'price' => $course->price,
                    'image_url' => $course->image ? Storage::disk('r2')->url($student->organization_id . '/course_images/' . $course->image) : null,
                    'category' => $course->courseCategory ? [
                        'id' => $course->courseCategory->id,
                        'name' => $course->courseCategory->name,
                        'slug' => $course->courseCategory->slug
                    ] : null,
                    'sub_category' => $course->courseSubCategory ? [
                        'id' => $course->courseSubCategory->id,
                        'name' => $course->courseSubCategory->name,
                        'slug' => $course->courseSubCategory->slug
                    ] : null,
                    'tags' => $course->tags,
                    'is_featured' => $course->is_featured,
                    'enrolled_students_count' => $course->students->count(),
                    'is_enrolled' => $course->students->contains($student->id),
                    'created_at' => $course->created_at->format('Y-m-d H:i:s')
                ];
            });

            return $this->success('Search results retrieved successfully', [
                'query' => $query,
                'total_results' => $courses->count(),
                'courses' => $courses
            ]);

        } catch (\Exception $e) {
            return $this->error('Error searching courses', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get course statistics
     */
    public function statistics(Request $request)
    {
        try {
            $student = $request->user('student');
            
            $totalCourses = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->count();

            $featuredCourses = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->where('is_featured', true)
                ->count();

            $freeCourses = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->where('price', 0)
                ->count();

            $paidCourses = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->where('price', '>', 0)
                ->count();

            $categoriesCount = CourseCategory::where('organization_id', $student->organization_id)
                ->where('is_active', true)
                ->count();

            $subCategoriesCount = CourseSubCategory::where('organization_id', $student->organization_id)
                ->where('is_active', true)
                ->count();

            $enrolledCourses = Course::whereHas('students', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })->where('organization_id', $student->organization_id)
              ->where('is_published', true)
              ->where('is_active', true)
              ->where('is_archived', false)
              ->count();

            return $this->success('Statistics retrieved successfully', [
                'statistics' => [
                    'total_courses' => $totalCourses,
                    'featured_courses' => $featuredCourses,
                    'free_courses' => $freeCourses,
                    'paid_courses' => $paidCourses,
                    'categories_count' => $categoriesCount,
                    'sub_categories_count' => $subCategoriesCount,
                    'enrolled_courses' => $enrolledCourses
                ]
            ]);

        } catch (\Exception $e) {
            return $this->error('Error retrieving statistics', ['error' => $e->getMessage()]);
        }
    }
}
