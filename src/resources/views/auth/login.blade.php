<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WeatherInsight</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes drift {
            0%, 100% {
                transform: translateX(0px);
            }
            50% {
                transform: translateX(30px);
            }
        }

        .float-cloud {
            animation: float 6s ease-in-out infinite;
        }

        .drift-cloud {
            animation: drift 8s ease-in-out infinite;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #e0f4ff 0%, #b3e5ff 25%, #81d4fa 50%, #4fc3f7 75%, #29b6f6 100%);
            position: relative;
            overflow: hidden;
        }

        .sun {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle at 35% 35%, #ffeb3b, #fbc02d);
            border-radius: 50%;
            box-shadow: 0 0 40px rgba(255, 235, 59, 0.3);
        }

        .cloud-shape {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 100px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        input[type="email"], input[type="password"] {
            transition: all 0.3s ease;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #0097a7;
            box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0097a7 0%, #006064 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 150, 136, 0.3);
        }

        .btn-google {
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
        }

        .btn-google:hover {
            border-color: #4285f4;
            background-color: #fafafa;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            margin: 0 12px;
            color: #9e9e9e;
            font-size: 14px;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center px-4 py-8 relative">

    <!-- Animated Cloud Background Elements -->
    <div class="absolute top-10 left-5 md:left-20 float-cloud">
        <div class="cloud-shape" style="width: 120px; height: 40px;"></div>
    </div>

    <div class="absolute top-32 right-10 md:right-20 drift-cloud" style="animation-delay: 1s;">
        <div class="cloud-shape" style="width: 100px; height: 35px;"></div>
    </div>

    <div class="absolute bottom-20 left-10 md:left-32 float-cloud" style="animation-delay: 2s;">
        <div class="cloud-shape" style="width: 90px; height: 30px;"></div>
    </div>

    <!-- Sun Element -->
    <div class="absolute top-20 right-5 md:right-32">
        <div class="sun"></div>
    </div>

    <!-- Main Login Container -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 md:p-10 z-10 backdrop-filter">

        <!-- Logo & Title Section -->
        <div class="mb-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-400 to-teal-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
                WeatherInsight
            </h1>

            <p class="text-gray-500 text-sm md:text-base">
                Real-time Weather Monitoring Dashboard
            </p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                <div class="font-semibold mb-1">Login Failed</div>
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="/login" class="space-y-5">
            @csrf

            <!-- Email Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="you@example.com"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-cyan-500"
                >
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-cyan-500"
                >
            </div>

            <!-- Login Button -->
            <button
                type="submit"
                class="btn-primary w-full py-3 px-4 rounded-lg bg-gradient-to-r from-cyan-500 to-teal-600 text-white font-bold text-center hover:shadow-lg"
            >
                Sign In
            </button>
        </form>

        <!-- Sign Up Link -->
        <div class="mt-8 text-center text-gray-600">
            <p class="text-sm">
                Don't have an account yet? 
                <a href="/register" class="text-teal-600 font-bold hover:text-teal-700">
                    Create account
                </a>
            </p>
        </div>

        <!-- Footer Note -->
        <div class="mt-6 text-center text-xs text-gray-400 border-t pt-5">
            <p>© 2026 WeatherInsight. All rights reserved.</p>
        </div>

    </div>

</body>
</html>