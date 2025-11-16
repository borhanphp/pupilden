@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </h2>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Students</h6>
                            <h3 class="card-title mb-0">{{ number_format($totalStudents) }}</h3>
                            <small class="text-white-50">{{ $activeStudents }} active</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Courses</h6>
                            <h3 class="card-title mb-0">{{ number_format($totalCourses) }}</h3>
                            <small class="text-white-50">{{ $publishedCourses }} published</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Pending Requests</h6>
                            <h3 class="card-title mb-0">{{ number_format($pendingRequests) }}</h3>
                            <small class="text-white-50">{{ $approvedEnrollments }} approved</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total Revenue</h6>
                            <h3 class="card-title mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                            <small class="text-white-50">${{ number_format($monthlyRevenue, 2) }} this month</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Revenue Trend (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Course Enrollments
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics and Lists -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star"></i> Top Courses by Enrollment
                        </h5>
                        <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($topCourses as $course)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $course->name }}</h6>
                                <small class="text-muted">{{ $course->courseCategory->name ?? 'Uncategorized' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">{{ $course->approved_students_count ?? 0 }} students</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No courses yet</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell"></i> Recent Enrollment Requests
                        </h5>
                        <a href="{{ route('student-courses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recentRequests as $request)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $request->student->name }}</h6>
                                <small class="text-muted">{{ $request->course->name }}</small>
                                <br>
                                <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                            </div>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                    @empty
                        <p class="text-muted text-center">No pending requests</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart"></i> Recent Purchases
                        </h5>
                        <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->student->name }}</td>
                                        <td>{{ $purchase->course->name }}</td>
                                        <td>${{ number_format($purchase->final_price, 2) }}</td>
                                        <td>{{ $purchase->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ ucfirst($purchase->payment_status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No purchases yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = @json($revenueChartData);
        const revenueLabels = revenueData.map(item => {
            const date = new Date(item.month);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        const revenueValues = revenueData.map(item => parseFloat(item.revenue || 0));

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenueValues,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
        const enrollmentData = @json($enrollmentChartData);
        const enrollmentLabels = enrollmentData.map(item => {
            const date = new Date(item.month);
            return date.toLocaleDateString('en-US', { month: 'short' });
        });
        const enrollmentValues = enrollmentData.map(item => parseInt(item.count || 0));

        new Chart(enrollmentCtx, {
            type: 'bar',
            data: {
                labels: enrollmentLabels,
                datasets: [{
                    label: 'Enrollments',
                    data: enrollmentValues,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endsection
