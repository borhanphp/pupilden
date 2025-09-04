<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ env('APP_NAME') }}</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="{{asset('assets/img/kaiadmin/favicon.ico')}}"
      type="image/x-icon"
    />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Font Awesome 6 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }
        .form-group label {
            position: absolute;
            top: 0px;
            left: 26px;
            font-size: 15px;
            font-weight: bold;
            color: #31009e !important;
            background-color: #fff !important;
        }
        .form-group .form-control {
            padding: 10px 20px;
            border-radius: 5px;
            border: 1px solid #31009e;
            background-color: #fff !important;
        }
        .form-group {
            position: relative;
        }
        .form-check-label{
          position: static !important;
        }
        .main-panel {
          position: static !important;
          width: 100%;
          float: none !important;
        }
        @stack('styles')
    </style>
  </head>
  <body>
    <div class="wrapper">
      
      <div class="main-panel">
        

        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">@yield('title')</h3>
            </div>
            
            @yield('content')
          </div>
        </div>

        <footer class="footer">
          <div class="container-fluid d-flex justify-content-end">
            
            <div class="copyright">
              2025, made with <i class="fas fa-heart text-danger"></i> by
              <a href="">Pupilden</a>
            </div>
            
          </div>
        </footer>
      </div>

      
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    @stack('scripts')
  </body>
</html>
