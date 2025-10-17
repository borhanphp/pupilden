<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\CoursePurchase;
use App\Models\Course;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with(['organization', 'courses', 'coursePurchases']);

        // Filter by organization if user is not superadmin
        if (Auth::user()->userRole->name !== 'superadmin') {
            $query->where('organization_id', Auth::user()->organization_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_number', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by organization
        if ($request->has('organization_id') && $request->organization_id) {
            $query->where('organization_id', $request->organization_id);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(15);
        $organizations = Organization::all();

        return view('students.index', compact('students', 'organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizations = Organization::all();
        return view('students.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'username' => 'required|string|max:255|unique:students,username',
            'email' => 'required|email|max:255|unique:students,email',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'alt_contact_number' => 'nullable|string|max:20',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('students/profile-pictures', $imageName, 'public');
            $data['profile_picture'] = $imagePath;
        }

        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['organization', 'courses', 'coursePurchases.course', 'coursePurchases.coupon']);
        
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $organizations = Organization::all();
        return view('students.edit', compact('student', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'username' => 'required|string|max:255|unique:students,username,' . $student->id,
            'email' => 'required|email|max:255|unique:students,email,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'alt_contact_number' => 'nullable|string|max:20',
        ]);

        $data = $request->all();

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
                Storage::disk('public')->delete($student->profile_picture);
            }

            $image = $request->file('profile_picture');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('students/profile-pictures', $imageName, 'public');
            $data['profile_picture'] = $imagePath;
        }

        $student->update($data);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Delete profile picture if exists
        if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
            Storage::disk('public')->delete($student->profile_picture);
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Display student's payment history
     */
    public function payments(Student $student)
    {
        $payments = $student->coursePurchases()
            ->with(['course', 'coupon'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('students.payments', compact('student', 'payments'));
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, CoursePurchase $purchase)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();

        // Update payment details
        $paymentDetails = $purchase->payment_details ?? [];
        if ($request->filled('notes')) {
            $paymentDetails['admin_notes'] = $request->notes;
        }

        $purchase->update([
            'payment_status' => $request->payment_status,
            'transaction_id' => $request->transaction_id,
            'payment_method' => $request->payment_method,
            'payment_details' => $paymentDetails,
        ]);

        // Handle enrollment/unenrollment based on status
        if ($request->payment_status === 'completed') {
            $purchase->student->courses()->syncWithoutDetaching([$purchase->course_id]);
        } elseif ($request->payment_status === 'refunded') {
            $purchase->student->courses()->detach($purchase->course_id);
        }

        return redirect()->back()
            ->with('success', 'Payment status updated successfully.');
    }

    /**
     * Get student statistics
     */
    public function statistics(Student $student)
    {
        $stats = [
            'total_courses' => $student->courses->count(),
            'total_purchases' => $student->coursePurchases->count(),
            'completed_payments' => $student->coursePurchases()->where('payment_status', 'completed')->count(),
            'pending_payments' => $student->coursePurchases()->where('payment_status', 'pending')->count(),
            'failed_payments' => $student->coursePurchases()->where('payment_status', 'failed')->count(),
            'refunded_payments' => $student->coursePurchases()->where('payment_status', 'refunded')->count(),
            'total_spent' => $student->coursePurchases()->where('payment_status', 'completed')->sum('final_price'),
        ];

        return response()->json($stats);
    }

    /**
     * Toggle student status (active/inactive)
     */
    public function toggleStatus(Student $student)
    {
        $student->update([
            'is_active' => !$student->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Student status updated successfully.');
    }

    /**
     * Reset student password
     */
    public function resetPassword(Request $request, Student $student)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $student->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()
            ->with('success', 'Student password reset successfully.');
    }
}
