<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('layouts.head')
    <style>
        .login-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-box {
            width: 350px; /* تصغير حجم الصندوق */
            margin: 0 auto;
        }
        
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-card-body {
            padding: 2rem; /* تقليل المساحة الداخلية */
        }
        
        .login-box-msg {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 1.5rem; /* تقليل الهامش */
            text-align: center;
            font-weight: 500;
        }
        
        .input-group {
            margin-bottom: 1.2rem; /* تقليل المسافة بين الحقول */
        }
        
        .form-control {
            height: 42px; /* تصغير ارتفاع حقول الإدخال */
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.375rem 1rem;
            font-size: 0.9rem; /* تصغير حجم الخط */
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0 0.75rem; /* تصغير حجم الأيقونات */
        }
        
        .btn-primary {
            background-color: #4e73df;
            border: none;
            padding: 10px; /* تصغير حجم الزر */
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s;
            width: 60%; /* جعل الزر أصغر عرضاً */
            margin: 0 auto; /* توسيط الزر */
            display: block; /* ضروري للتوسيط */
        }
        
        .btn-primary:hover {
            background-color: #3a5ccc;
            transform: translateY(-2px);
        }
        
        .bi {
            color: #6c757d;
            font-size: 0.9rem; /* تصغير حجم الأيقونات */
        }
    </style>
</head>

<body class="login-page">
    <div class="login-box">
        <div class="card login-card">
            <div class="card-body login-card-body">
                <h3 class="login-box-msg">تسجيل الدخول</h3>
                      <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- حقل رقم الهاتف -->
                    <div class="input-group">
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required autofocus autocomplete="phone" class="form-control" placeholder="رقم الهاتف">
                        <div class="input-group-text">
                            <span class="bi bi-phone"></span>
                        </div>
                    </div>
                    @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror

                    <!-- حقل كلمة المرور -->
                    <div class="input-group">
                        <input type="password" name="password" id="password" required autocomplete="current-password" class="form-control" placeholder="كلمة المرور">
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror

                    <!-- زر تسجيل الدخول -->
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i> تسجيل الدخول
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.footer-scripts')
</body>

</html>