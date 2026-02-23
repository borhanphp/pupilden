<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google OAuth Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Google OAuth Error</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $message }}</p>
                        <a href="{{ url('/') }}" class="btn btn-primary">Back to home</a>
                        <a href="{{ url('/google/oauth') }}" class="btn btn-outline-secondary">Try again</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
