<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google OAuth – Refresh Token</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Google OAuth – Refresh token</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Add this line to your <code>.env</code> file and run <code>php artisan config:clear</code>.</p>
                        <div class="bg-dark text-light p-3 rounded mb-3">
                            <code class="text-break">{{ $env_line }}</code>
                        </div>
                        <p class="small text-muted">Or copy only the value and set <code>GOOGLE_REFRESH_TOKEN=</code> in .env</p>
                        <hr>
                        <p class="mb-0"><strong>Refresh token (value only):</strong></p>
                        <div class="bg-light p-2 rounded">
                            <code id="token-value" class="text-break">{{ $refresh_token }}</code>
                        </div>
                        <div class="mt-3">
                            <a href="{{ url('/') }}" class="btn btn-primary">Back to home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
