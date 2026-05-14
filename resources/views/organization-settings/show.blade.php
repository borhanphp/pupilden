@extends('layouts.app')

@section('title', 'Organization Settings')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Organization Settings
                        </h4>
                        <div>
                            <a href="{{ route('organization-settings.edit', $organizationSetting) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Settings
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Branding & Design</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Logo</th>
                                    <td>
                                        @if($organizationSetting->logo)
                                            <img src="{{ \Storage::disk('r2')->url($organizationSetting->logo) }}" alt="Logo" class="img-thumbnail" style="max-width: 150px;">
                                        @else
                                            <span class="text-muted">No logo</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Favicon</th>
                                    <td>
                                        @if($organizationSetting->favicon)
                                            <img src="{{ \Storage::disk('r2')->url($organizationSetting->favicon) }}" alt="Favicon" class="img-thumbnail" style="max-width: 50px;">
                                        @else
                                            <span class="text-muted">No favicon</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Template</th>
                                    <td>{{ $organizationSetting->template ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Primary Color</th>
                                    <td>
                                        @if($organizationSetting->primary_color)
                                            <span class="badge" style="background-color: {{ $organizationSetting->primary_color }}; color: white;">
                                                {{ $organizationSetting->primary_color }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Footer Color</th>
                                    <td>
                                        @if($organizationSetting->footer_color)
                                            <span class="badge" style="background-color: {{ $organizationSetting->footer_color }}; color: white;">
                                                {{ $organizationSetting->footer_color }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Footer Design</th>
                                    <td>{{ $organizationSetting->footer_design ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Content & Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Banner</th>
                                    <td>
                                        @if($organizationSetting->banner)
                                            <img src="{{ \Storage::disk('r2')->url($organizationSetting->banner) }}" alt="Banner" class="img-thumbnail" style="max-width: 150px;">
                                        @else
                                            <span class="text-muted">No banner</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hero Text</th>
                                    <td>{{ $organizationSetting->hero_text ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Business Email</th>
                                    <td>{{ $organizationSetting->business_email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Copyright Text</th>
                                    <td>{{ $organizationSetting->copyright_text ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $organizationSetting->created_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $organizationSetting->updated_at->format('F j, Y, g:i a') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">About Us Content</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p>{{ $organizationSetting->about_us_content ?? 'No content set.' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Privacy Policy Content</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p>{{ $organizationSetting->privacy_policy_content ?? 'No content set.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Payment Gateway Numbers</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="25%">Baksh Number</th>
                                    <td>{{ $organizationSetting->baksh_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nagad Number</th>
                                    <td>{{ $organizationSetting->ngad_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Rocket Number</th>
                                    <td>{{ $organizationSetting->rocket_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Celfin Number</th>
                                    <td>{{ $organizationSetting->celfin_number ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('organization-settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <div>
                                    <a href="{{ route('organization-settings.edit', $organizationSetting) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Settings
                                    </a>
                                    <form action="{{ route('organization-settings.destroy', $organizationSetting) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete these settings?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

