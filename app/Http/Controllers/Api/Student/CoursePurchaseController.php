<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Coupon;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CoursePurchaseController extends Controller
{
    /**
     * Purchase a course with or without coupon
     */
    public function purchase(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|exists:courses,id',
                'coupon_code' => 'nullable|string|max:50',
                'payment_method' => 'required|string',
                'payment_details' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();
            $courseId = $request->course_id;
            $couponCode = $request->coupon_code;

            // Get the course
            $course = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->findOrFail($courseId);

            // Check if student already purchased this course
            if ($course->isPurchasedBy($student->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already purchased this course'
                ], 400);
            }

            // Check if student is already enrolled (free course)
            if ($course->isEnrolledBy($student->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this course'
                ], 400);
            }

            $originalPrice = $course->price;
            $discountAmount = 0;
            $coupon = null;

            // Process coupon if provided
            if ($couponCode) {
                $coupon = Coupon::where('organization_id', $student->organization_id)
                    ->where('code', $couponCode)
                    ->where('is_active', true)
                    ->first();

                if (!$coupon) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid coupon code'
                    ], 400);
                }

                if (!$coupon->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Coupon is not valid or has expired'
                    ], 400);
                }

                if (!$coupon->isApplicableToCourse($courseId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This coupon is not applicable to this course'
                    ], 400);
                }

                if (!$coupon->meetsMinimumAmount($originalPrice)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum order amount not met for this coupon'
                    ], 400);
                }

                $discountAmount = $coupon->calculateDiscount($originalPrice);
            }

            $finalPrice = $originalPrice - $discountAmount;

            // Create purchase record
            $purchase = CoursePurchase::create([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'coupon_id' => $coupon?->id,
                'original_price' => $originalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_details' => $request->payment_details ?? []
            ]);

            // For now, we'll simulate a successful payment
            // In a real application, you would integrate with payment gateways here
            $transactionId = 'TXN_' . time() . '_' . $purchase->id;
            
            // Mark as completed and enroll student
            //$purchase->markAsCompleted($transactionId, $request->payment_method);

            // Increment coupon usage if used
            if ($coupon) {
                $coupon->incrementUsage();
            }

            return response()->json([
                'success' => true,
                'message' => 'Course purchased successfully',
                'data' => [
                    'purchase_id' => $purchase->id,
                    'course' => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'slug' => $course->slug,
                        'image_url' => $course->image ? Storage::disk('r2')->url($course->organization_id . '/course_images/' . $course->image) : null,
                    ],
                    'pricing' => [
                        'original_price' => $originalPrice,
                        'discount_amount' => $discountAmount,
                        'final_price' => $finalPrice,
                    ],
                    'coupon' => $coupon ? [
                        'code' => $coupon->code,
                        'name' => $coupon->name,
                        'type' => $coupon->type,
                        'value' => $coupon->value,
                    ] : null,
                    'transaction_id' => $transactionId,
                    'payment_status' => 'completed',
                    'purchased_at' => $purchase?->purchased_at?->toISOString(),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate coupon code
     */
    public function validateCoupon(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'coupon_code' => 'required|string|max:50',
                'course_id' => 'required|exists:courses,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();
            $couponCode = $request->coupon_code;
            $courseId = $request->course_id;

            // Get the course
            $course = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->findOrFail($courseId);

            // Find coupon
            $coupon = Coupon::where('organization_id', $student->organization_id)
                ->where('code', $couponCode)
                ->where('is_active', true)
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code'
                ], 400);
            }

            if (!$coupon->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon is not valid or has expired'
                ], 400);
            }

            if (!$coupon->isApplicableToCourse($courseId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon is not applicable to this course'
                ], 400);
            }

            if (!$coupon->meetsMinimumAmount($course->price)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum order amount not met for this coupon'
                ], 400);
            }

            $discountAmount = $coupon->calculateDiscount($course->price);
            $finalPrice = $course->price - $discountAmount;

            return response()->json([
                'success' => true,
                'message' => 'Coupon is valid',
                'data' => [
                    'coupon' => [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'name' => $coupon->name,
                        'description' => $coupon->description,
                        'type' => $coupon->type,
                        'type_label' => $coupon->type_label,
                        'value' => $coupon->value,
                        'minimum_amount' => $coupon->minimum_amount,
                        'maximum_discount' => $coupon->maximum_discount,
                    ],
                    'pricing' => [
                        'original_price' => $course->price,
                        'discount_amount' => $discountAmount,
                        'final_price' => $finalPrice,
                        'savings_percentage' => $course->price > 0 ? round(($discountAmount / $course->price) * 100, 2) : 0,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validating coupon: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's purchase history
     */
    public function purchaseHistory(Request $request)
    {
        try {
            $student = auth('student')->user();
            
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status'); // pending, completed, failed, refunded

            $query = CoursePurchase::with(['course', 'coupon'])
                ->where('student_id', $student->id);

            if ($status) {
                $query->where('payment_status', $status);
            }

            $purchases = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $purchases->getCollection()->transform(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'course' => [
                        'id' => $purchase->course->id,
                        'name' => $purchase->course->name,
                        'slug' => $purchase->course->slug,
                        'image_url' => $purchase->course->image ? Storage::disk('r2')->url($purchase->course->organization_id . '/course_images/' . $purchase->course->image) : null,
                    ],
                    'coupon' => $purchase->coupon ? [
                        'code' => $purchase->coupon->code,
                        'name' => $purchase->coupon->name,
                        'type' => $purchase->coupon->type,
                        'value' => $purchase->coupon->value,
                    ] : null,
                    'pricing' => [
                        'original_price' => $purchase->original_price,
                        'discount_amount' => $purchase->discount_amount,
                        'final_price' => $purchase->final_price,
                    ],
                    'payment_status' => $purchase->payment_status,
                    'payment_status_label' => $purchase->payment_status_label,
                    'payment_method' => $purchase->payment_method,
                    'transaction_id' => $purchase->transaction_id,
                    'purchased_at' => $purchase->purchased_at?->toISOString(),
                    'created_at' => $purchase->created_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $purchases
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving purchase history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available coupons for a course
     */
    public function availableCoupons(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|exists:courses,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = auth('student')->user();
            $courseId = $request->course_id;

            // Get the course
            $course = Course::where('organization_id', $student->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->findOrFail($courseId);

            // Get available coupons
            $coupons = Coupon::where('organization_id', $student->organization_id)
                ->where('is_active', true)
                ->where(function($query) use ($courseId) {
                    $query->whereNull('applicable_courses')
                          ->orWhereJsonContains('applicable_courses', $courseId);
                })
                ->where(function($query) {
                    $query->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                })
                ->where(function($query) {
                    $query->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                })
                ->get()
                ->filter(function($coupon) use ($course) {
                    return $coupon->meetsMinimumAmount($course->price);
                })
                ->map(function($coupon) use ($course) {
                    $discountAmount = $coupon->calculateDiscount($course->price);
                    return [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'name' => $coupon->name,
                        'description' => $coupon->description,
                        'type' => $coupon->type,
                        'type_label' => $coupon->type_label,
                        'value' => $coupon->value,
                        'minimum_amount' => $coupon->minimum_amount,
                        'maximum_discount' => $coupon->maximum_discount,
                        'discount_amount' => $discountAmount,
                        'final_price' => $course->price - $discountAmount,
                        'savings_percentage' => $course->price > 0 ? round(($discountAmount / $course->price) * 100, 2) : 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'course' => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'price' => $course->price,
                    ],
                    'coupons' => $coupons
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving available coupons: ' . $e->getMessage()
            ], 500);
        }
    }
}
