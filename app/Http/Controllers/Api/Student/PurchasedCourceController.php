<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoursePurchase;
use Illuminate\Support\Facades\Storage;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use App\Models\Organization;
use App\Models\Student;
use App\Models\CourseModule;
use App\Models\CourseModuleFile;
use App\Models\Video;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\Validator;

class PurchasedCourceController extends Controller
{
    /**
     * Get student's purchased courses with category filtering
     */
    public function index(Request $request)
    {
        try {
            $student = auth('student')->user();
            
            $validator = Validator::make($request->all(), [
                'category_id' => 'nullable|exists:course_categories,id',
                'sub_category_id' => 'nullable|exists:course_sub_categories,id',
                'search' => 'nullable|string|max:255',
                'per_page' => 'nullable|integer|min:1|max:100',
                'sort_by' => 'nullable|in:name,price,purchased_at,created_at',
                'sort_order' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $perPage = $request->get('per_page', 15);
            $sortBy = $request->get('sort_by', 'purchased_at');
            $sortOrder = $request->get('sort_order', 'desc');

            // Get purchased courses with relationships
            $query = CoursePurchase::with([
                'course.courseCategory',
                'course.courseSubCategory',
                'course.organization',
                'coupon'
            ])
            ->where('student_id', $student->id)
            ->where('payment_status', 'completed');

            // Apply filters
            if ($request->has('category_id') && $request->category_id) {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('course_category_id', $request->category_id);
                });
            }

            if ($request->has('sub_category_id') && $request->sub_category_id) {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('course_sub_category_id', $request->sub_category_id);
                });
            }

            if ($request->has('search') && $request->search) {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            // Apply sorting
            if ($sortBy === 'name') {
                $query->join('courses', 'course_purchases.course_id', '=', 'courses.id')
                      ->orderBy('courses.name', $sortOrder)
                      ->select('course_purchases.*');
            } elseif ($sortBy === 'price') {
                $query->orderBy('final_price', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            $purchases = $query->paginate($perPage);

            // Transform the data
            $purchases->getCollection()->transform(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'course' => [
                        'id' => $purchase->course->id,
                        'name' => $purchase->course->name,
                        'slug' => $purchase->course->slug,
                        'description' => $purchase->course->description,
                        'image_url' => $purchase->course->image ? Storage::disk('r2')->url($purchase->course->organization_id . '/course_images/' . $purchase->course->image) : null,
                        'duration' => $purchase->course->duration,
                        'level' => $purchase->course->level,
                        'language' => $purchase->course->language,
                        'price' => $purchase->course->price,
                        'is_featured' => $purchase->course->is_featured,
                        'category' => $purchase->course->courseCategory ? [
                            'id' => $purchase->course->courseCategory->id,
                            'name' => $purchase->course->courseCategory->name,
                        ] : null,
                        'sub_category' => $purchase->course->courseSubCategory ? [
                            'id' => $purchase->course->courseSubCategory->id,
                            'name' => $purchase->course->courseSubCategory->name,
                        ] : null,
                        'organization' => [
                            'id' => $purchase->course->organization->id,
                            'name' => $purchase->course->organization->name,
                        ],
                        'modules_count' => $purchase->course->modules()->count(),
                        'videos_count' => $purchase->course->videos()->count(),
                        'exams_count' => $purchase->course->exams()->count(),
                    ],
                    'purchase_details' => [
                        'original_price' => $purchase->original_price,
                        'discount_amount' => $purchase->discount_amount,
                        'final_price' => $purchase->final_price,
                        'payment_method' => $purchase->payment_method,
                        'transaction_id' => $purchase->transaction_id,
                        'purchased_at' => $purchase->purchased_at?->toISOString(),
                    ],
                    'coupon' => $purchase->coupon ? [
                        'id' => $purchase->coupon->id,
                        'code' => $purchase->coupon->code,
                        'name' => $purchase->coupon->name,
                        'type' => $purchase->coupon->type,
                        'value' => $purchase->coupon->value,
                    ] : null,
                ];
            });

            // Get available categories for filtering
            $categories = CourseCategory::whereHas('courses', function($q) use ($student) {
                $q->whereHas('coursePurchases', function($purchaseQuery) use ($student) {
                    $purchaseQuery->where('student_id', $student->id)
                                 ->where('payment_status', 'completed');
                });
            })->get(['id', 'name']);

            $subCategories = CourseSubCategory::whereHas('courses', function($q) use ($student) {
                $q->whereHas('coursePurchases', function($purchaseQuery) use ($student) {
                    $purchaseQuery->where('student_id', $student->id)
                                 ->where('payment_status', 'completed');
                });
            })->get(['id', 'name', 'course_category_id']);

            return response()->json([
                'success' => true,
                'data' => [
                    'purchases' => $purchases,
                    'filters' => [
                        'categories' => $categories,
                        'sub_categories' => $subCategories,
                    ],
                    'statistics' => [
                        'total_courses' => $purchases->total(),
                        'total_spent' => CoursePurchase::where('student_id', $student->id)
                            ->where('payment_status', 'completed')
                            ->sum('final_price'),
                        'categories_count' => $categories->count(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving purchased courses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed information about a purchased course
     */
    public function show(Request $request, $courseId)
    {
        try {
            $student = auth('student')->user();

            $purchase = CoursePurchase::with([
                'course.courseCategory',
                'course.courseSubCategory',
                'course.organization',
                'course.modules.files',
                'course.videos',
                'course.exams.questions',
                'coupon'
            ])
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('payment_status', 'completed')
            ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found or not purchased'
                ], 404);
            }

            $course = $purchase->course;

            return response()->json([
                'success' => true,
                'data' => [
                    'purchase' => [
                        'id' => $purchase->id,
                        'original_price' => $purchase->original_price,
                        'discount_amount' => $purchase->discount_amount,
                        'final_price' => $purchase->final_price,
                        'payment_method' => $purchase->payment_method,
                        'transaction_id' => $purchase->transaction_id,
                        'purchased_at' => $purchase->purchased_at?->toISOString(),
                    ],
                    'course' => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'slug' => $course->slug,
                        'description' => $course->description,
                        'image_url' => $course->image ? Storage::disk('r2')->url($course->organization_id . '/course_images/' . $course->image) : null,
                        'duration' => $course->duration,
                        'level' => $course->level,
                        'language' => $course->language,
                        'price' => $course->price,
                        'is_featured' => $course->is_featured,
                        'category' => $course->courseCategory ? [
                            'id' => $course->courseCategory->id,
                            'name' => $course->courseCategory->name,
                        ] : null,
                        'sub_category' => $course->courseSubCategory ? [
                            'id' => $course->courseSubCategory->id,
                            'name' => $course->courseSubCategory->name,
                        ] : null,
                        'organization' => [
                            'id' => $course->organization->id,
                            'name' => $course->organization->name,
                        ],
                        'modules' => $course->modules->map(function($module) {
                            return [
                                'id' => $module->id,
                                'name' => $module->name,
                                'description' => $module->description,
                                'order' => $module->order,
                                'duration' => $module->duration,
                                'duration_type' => $module->duration_type,
                                'status' => $module->status,
                                'files_count' => $module->files->count(),
                                'files' => $module->files->map(function($file) {
                                    return [
                                        'id' => $file->id,
                                        'name' => $file->name,
                                        'file_type' => $file->file_type,
                                        'file_size' => $file->file_size,
                                        'file_url' => $file->file_url,
                                        'file_extension' => $file->file_extension,
                                    ];
                                })
                            ];
                        }),
                        'videos' => $course->videos->map(function($video) {
                            return [
                                'id' => $video->id,
                                'title' => $video->title,
                                'description' => $video->description,
                                'video_url' => $video->video_url,
                                'duration' => $video->duration,
                                'order' => $video->order,
                                'is_published' => $video->is_published,
                            ];
                        }),
                        'exams' => $course->exams->map(function($exam) {
                            return [
                                'id' => $exam->id,
                                'title' => $exam->title,
                                'description' => $exam->description,
                                'duration' => $exam->duration,
                                'total_questions' => $exam->questions->count(),
                                'passing_score' => $exam->passing_score,
                                'is_published' => $exam->is_published,
                            ];
                        }),
                    ],
                    'coupon' => $purchase->coupon ? [
                        'id' => $purchase->coupon->id,
                        'code' => $purchase->coupon->code,
                        'name' => $purchase->coupon->name,
                        'type' => $purchase->coupon->type,
                        'value' => $purchase->coupon->value,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving course details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course modules for a purchased course
     */
    public function modules(Request $request, $courseId)
    {
        try {
            $student = auth('student')->user();

            // Verify student has purchased this course
            $purchase = CoursePurchase::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->where('payment_status', 'completed')
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found or not purchased'
                ], 404);
            }

            $modules = CourseModule::with(['files'])
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $modules->map(function($module) use ($student) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'description' => $module->description,
                        'image_url' => $module->image ? Storage::disk('r2')->url($student->organization_id . '/course_modules/' . $module->image) : null,
                        'order' => $module->order,
                        'duration' => $module->duration,
                        'duration_type' => $module->duration_type,
                        'duration_value' => $module->duration_value,
                        'status' => $module->status,
                        'files' => $module->files->map(function($file) {
                            return [
                                'id' => $file->id,
                                'name' => $file->name,
                                'file_type' => $file->file_type,
                                'file_size' => $file->file_size,
                                'file_url' => $file->file_url,
                                'file_extension' => $file->file_extension,
                                'file_mime_type' => $file->file_mime_type,
                            ];
                        })
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving course modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get module-wise video list with video details and module files
     */
    public function moduleVideos(Request $request, $courseId)
    {
        try {
            $student = auth('student')->user();

            // Verify student has purchased this course
            $purchase = CoursePurchase::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->where('payment_status', 'completed')
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found or not purchased'
                ], 404);
            }

            // Get course modules with videos and files
            $modules = CourseModule::with([
                'files',
                'videos' => function($query) {
                    $query->where('is_published', true)->orderBy('order');
                }
            ])
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->orderBy('order')
            ->get();

            // Get videos that don't belong to any module (standalone videos)
            $standaloneVideos = Video::where('course_id', $courseId)
                ->where('is_published', true)
                ->whereNull('course_module_id')
                ->orderBy('order')
                ->get();

            // Transform modules data
            $modulesData = $modules->map(function($module) use ($student) {
                return [
                    'module' => [
                        'id' => $module->id,
                        'name' => $module->name,
                        'description' => $module->description,
                        'image_url' => $module->image ? Storage::disk('r2')->url($student->organization_id . '/course_modules/' . $module->image) : null,
                        'order' => $module->order,
                        'duration' => $module->duration,
                        'duration_type' => $module->duration_type,
                        'duration_value' => $module->duration_value,
                        'status' => $module->status,
                    ],
                    'videos' => $module->videos->map(function($video) {
                        return [
                            'id' => $video->id,
                            'title' => $video->title,
                            'description' => $video->description,
                            'video_url' => $video->video_url,
                            'thumbnail_url' => $video->thumbnail_url,
                            'duration' => $video->duration,
                            'duration_formatted' => $this->formatDuration($video->duration),
                            'order' => $video->order,
                            'is_published' => $video->is_published,
                            'video_type' => $video->video_type ?? 'mp4',
                            'file_size' => $video->file_size,
                            'created_at' => $video->created_at->toISOString(),
                            'updated_at' => $video->updated_at->toISOString(),
                        ];
                    }),
                    'files' => $module->files->map(function($file) {
                        return [
                            'id' => $file->id,
                            'name' => $file->name,
                            'file_type' => $file->file_type,
                            'file_size' => $file->file_size,
                            'file_size_formatted' => $this->formatFileSize($file->file_size),
                            'file_url' => $file->file_url,
                            'file_extension' => $file->file_extension,
                            'file_mime_type' => $file->file_mime_type,
                            'created_at' => $file->created_at->toISOString(),
                        ];
                    }),
                    'statistics' => [
                        'videos_count' => $module->videos->count(),
                        'files_count' => $module->files->count(),
                        'total_duration' => $module->videos->sum('duration'),
                        'total_duration_formatted' => $this->formatDuration($module->videos->sum('duration')),
                    ]
                ];
            });

            // Transform standalone videos
            $standaloneVideosData = $standaloneVideos->map(function($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'video_url' => $video->video_url,
                    'thumbnail_url' => $video->thumbnail_url,
                    'duration' => $video->duration,
                    'duration_formatted' => $this->formatDuration($video->duration),
                    'order' => $video->order,
                    'is_published' => $video->is_published,
                    'video_type' => $video->video_type ?? 'mp4',
                    'file_size' => $video->file_size,
                    'created_at' => $video->created_at->toISOString(),
                    'updated_at' => $video->updated_at->toISOString(),
                ];
            });

            // Calculate overall statistics
            $totalVideos = $modules->sum(function($module) {
                return $module->videos->count();
            }) + $standaloneVideos->count();

            $totalFiles = $modules->sum(function($module) {
                return $module->files->count();
            });

            $totalDuration = $modules->sum(function($module) {
                return $module->videos->sum('duration');
            }) + $standaloneVideos->sum('duration');

            return response()->json([
                'success' => true,
                'data' => [
                    'course' => [
                        'id' => $courseId,
                        'name' => $purchase->course->name,
                    ],
                    'modules' => $modulesData,
                    'standalone_videos' => $standaloneVideosData,
                    'statistics' => [
                        'total_modules' => $modules->count(),
                        'total_videos' => $totalVideos,
                        'total_files' => $totalFiles,
                        'total_duration' => $totalDuration,
                        'total_duration_formatted' => $this->formatDuration($totalDuration),
                        'modules_with_videos' => $modules->filter(function($module) {
                            return $module->videos->count() > 0;
                        })->count(),
                        'modules_with_files' => $modules->filter(function($module) {
                            return $module->files->count() > 0;
                        })->count(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving module videos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get videos for a specific module
     */
    public function moduleVideosByModule(Request $request, $courseId, $moduleId)
    {
        try {
            $student = auth('student')->user();

            // Verify student has purchased this course
            $purchase = CoursePurchase::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->where('payment_status', 'completed')
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found or not purchased'
                ], 404);
            }

            // Get specific module with videos and files
            $module = CourseModule::with([
                'files',
                'videos' => function($query) {
                    $query->where('is_published', true)->orderBy('order');
                }
            ])
            ->where('course_id', $courseId)
            ->where('id', $moduleId)
            ->where('status', 'active')
            ->first();

            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module not found or not active'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'module' => [
                        'id' => $module->id,
                        'name' => $module->name,
                        'description' => $module->description,
                        'image_url' => $module->image ? Storage::disk('r2')->url($student->organization_id . '/course_modules/' . $module->image) : null,
                        'order' => $module->order,
                        'duration' => $module->duration,
                        'duration_type' => $module->duration_type,
                        'duration_value' => $module->duration_value,
                        'status' => $module->status,
                    ],
                    'videos' => $module->videos->map(function($video) {
                        return [
                            'id' => $video->id,
                            'title' => $video->title,
                            'description' => $video->description,
                            'video_url' => $video->video_url,
                            'thumbnail_url' => $video->thumbnail_url,
                            'duration' => $video->duration,
                            'duration_formatted' => $this->formatDuration($video->duration),
                            'order' => $video->order,
                            'is_published' => $video->is_published,
                            'video_type' => $video->video_type ?? 'mp4',
                            'file_size' => $video->file_size,
                            'created_at' => $video->created_at->toISOString(),
                            'updated_at' => $video->updated_at->toISOString(),
                        ];
                    }),
                    'files' => $module->files->map(function($file) {
                        return [
                            'id' => $file->id,
                            'name' => $file->name,
                            'file_type' => $file->file_type,
                            'file_size' => $file->file_size,
                            'file_size_formatted' => $this->formatFileSize($file->file_size),
                            'file_url' => $file->file_url,
                            'file_extension' => $file->file_extension,
                            'file_mime_type' => $file->file_mime_type,
                            'created_at' => $file->created_at->toISOString(),
                        ];
                    }),
                    'statistics' => [
                        'videos_count' => $module->videos->count(),
                        'files_count' => $module->files->count(),
                        'total_duration' => $module->videos->sum('duration'),
                        'total_duration_formatted' => $this->formatDuration($module->videos->sum('duration')),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving module videos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format duration in seconds to readable format
     */
    private function formatDuration($seconds)
    {
        if (!$seconds) return '0:00';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
    }

    /**
     * Format file size in bytes to readable format
     */
    private function formatFileSize($bytes)
    {
        if (!$bytes) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * store video watch history
     */
    
}