<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NEXORA Education</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="login-container">
        <div class="card login-card">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <div class="card-header login-header">
                <h4><i class="fas fa-graduation-cap me-2"></i>NEXORA Education</h4>
                <p class="mb-0">Welcome back! Please login to continue</p>
            </div>
            <div class="card-body login-body">
                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                            <div>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-info" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2 fs-5"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label login-form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <div class="input-group login-input-group" id="emailGroup">
                            <span class="input-group-text login-input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email"
                                class="form-control login-form-control @error('email') is-invalid @enderror" 
                                id="email"
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                autocomplete="email"
                                placeholder="Enter your email address"
                                aria-describedby="emailHelp">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @else
                            <div id="emailHelp" class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i>We'll never share your email with anyone else.
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label login-form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="input-group login-input-group">
                            <span class="input-group-text login-input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                class="form-control login-form-control @error('password') is-invalid @enderror"
                                id="password" 
                                name="password" 
                                required 
                                placeholder="Enter your password"
                                autocomplete="current-password">
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login text-white w-100 py-3 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Your Account
                    </button>

                    @if (Route::has('password.request'))
                        <div class="text-center mb-3">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                <i class="fas fa-key me-1"></i>Forgot Your Password?
                            </a>
                        </div>
                    @endif
                </form>

                <div class="login-footer-text">
                   
                    <p class="mb-0">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-user-plus me-1"></i>Contact Administrator
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your Custom JS -->
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>