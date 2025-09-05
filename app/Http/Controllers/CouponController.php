<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        try {
            $query = Coupon::with(['organization', 'coursePurchases'])
                ->where('organization_id', auth()->user()->organization_id);

            // Filter by status
            if ($request->has('status')) {
                $status = $request->status;
                switch ($status) {
                    case 'active':
                        $query->where('is_active', true)
                            ->where(function($q) {
                                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                            })
                            ->where(function($q) {
                                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                            })
                            ->where(function($q) {
                                $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
                            });
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                    case 'expired':
                        $query->where('expires_at', '<', now());
                        break;
                    case 'usage_limit_reached':
                        $query->whereRaw('used_count >= usage_limit');
                        break;
                }
            }

            // Search by code or name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            }

            $coupons = $query->orderBy('created_at', 'desc')->get();

            return view('coupons.index', compact('coupons'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving coupons: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create()
    {
        try {
            $courses = Course::where('organization_id', auth()->user()->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('name')
                ->get();

            return view('coupons.form', compact('courses'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created coupon
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:coupons,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'minimum_amount' => 'nullable|numeric|min:0',
                'maximum_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'usage_limit_per_user' => 'nullable|integer|min:1',
                'applicable_courses' => 'nullable|array',
                'applicable_courses.*' => 'exists:courses,id',
                'starts_at' => 'nullable|date|after_or_equal:today',
                'expires_at' => 'nullable|date|after:starts_at',
                'is_active' => 'boolean'
            ]);

            // Additional validation for percentage type
            if ($request->type === 'percentage' && $request->value > 100) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Percentage value cannot exceed 100%');
            }

            $couponData = [
                'organization_id' => auth()->user()->organization_id,
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'value' => $request->value,
                'minimum_amount' => $request->minimum_amount,
                'maximum_discount' => $request->maximum_discount,
                'usage_limit' => $request->usage_limit,
                'usage_limit_per_user' => $request->usage_limit_per_user,
                'applicable_courses' => $request->applicable_courses,
                'starts_at' => $request->starts_at,
                'expires_at' => $request->expires_at,
                'is_active' => $request->has('is_active'),
            ];

            $coupon = Coupon::create($couponData);

            return redirect()->route('coupons.index')
                ->with('success', 'Coupon created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating coupon: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        try {
            // Verify coupon belongs to organization
            if ($coupon->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $coupon->load(['organization', 'coursePurchases.student', 'coursePurchases.course']);

            return view('coupons.show', compact('coupon'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error retrieving coupon: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        try {
            // Verify coupon belongs to organization
            if ($coupon->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $courses = Course::where('organization_id', auth()->user()->organization_id)
                ->where('is_published', true)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->orderBy('name')
                ->get();

            return view('coupons.form', compact('coupon', 'courses'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        try {
            // Verify coupon belongs to organization
            if ($coupon->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $request->validate([
                'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'minimum_amount' => 'nullable|numeric|min:0',
                'maximum_discount' => 'nullable|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'usage_limit_per_user' => 'nullable|integer|min:1',
                'applicable_courses' => 'nullable|array',
                'applicable_courses.*' => 'exists:courses,id',
                'starts_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after:starts_at',
                'is_active' => 'boolean'
            ]);

            // Additional validation for percentage type
            if ($request->type === 'percentage' && $request->value > 100) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Percentage value cannot exceed 100%');
            }

            // Check if coupon has been used
            if ($coupon->used_count > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cannot edit coupon that has already been used');
            }

            $couponData = [
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'value' => $request->value,
                'minimum_amount' => $request->minimum_amount,
                'maximum_discount' => $request->maximum_discount,
                'usage_limit' => $request->usage_limit,
                'usage_limit_per_user' => $request->usage_limit_per_user,
                'applicable_courses' => $request->applicable_courses,
                'starts_at' => $request->starts_at,
                'expires_at' => $request->expires_at,
                'is_active' => $request->has('is_active'),
            ];

            $coupon->update($couponData);

            return redirect()->route('coupons.index')
                ->with('success', 'Coupon updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating coupon: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Coupon $coupon)
    {
        try {
            // Verify coupon belongs to organization
            if ($coupon->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            // Check if coupon has been used
            if ($coupon->used_count > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete coupon that has already been used');
            }

            $coupon->delete();

            return redirect()->route('coupons.index')
                ->with('success', 'Coupon deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting coupon: ' . $e->getMessage());
        }
    }

    /**
     * Toggle coupon active status
     */
    public function toggleActive(Coupon $coupon)
    {
        try {
            // Verify coupon belongs to organization
            if ($coupon->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $coupon->update(['is_active' => !$coupon->is_active]);

            $status = $coupon->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "Coupon {$status} successfully");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating coupon status: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a coupon
     */
    public function duplicate(Coupon $coupon)
    {
        try {
            // Verify coupon belongs to organization
            if ($coupon->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized');
            }

            $newCoupon = $coupon->replicate();
            $newCoupon->code = $coupon->code . '_COPY_' . time();
            $newCoupon->name = $coupon->name . ' (Copy)';
            $newCoupon->used_count = 0;
            $newCoupon->save();

            return redirect()->route('coupons.index')
                ->with('success', 'Coupon duplicated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error duplicating coupon: ' . $e->getMessage());
        }
    }
}
