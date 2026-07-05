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
        <div class="relative h-12 w-[140px]">
            <div class="cloud-shape absolute bottom-0 left-0 h-10 w-[120px]"></div>
            <div class="absolute -top-1 left-8 h-12 w-12 rounded-full bg-white/80 shadow-[0_8px_32px_rgba(0,0,0,0.05)]"></div>
            <div class="absolute top-1 right-4 h-9 w-9 rounded-full bg-white/80 shadow-[0_8px_32px_rgba(0,0,0,0.05)]"></div>
        </div>
    </div>

    <div class="absolute top-32 right-10 md:right-20 drift-cloud" style="animation-delay: 1s;">
        <div class="relative h-10 w-[118px]">
            <div class="cloud-shape absolute bottom-0 left-2 h-8 w-[98px]"></div>
            <div class="absolute -top-1 left-6 h-10 w-10 rounded-full bg-white/80 shadow-[0_8px_32px_rgba(0,0,0,0.05)]"></div>
            <div class="absolute top-0 right-4 h-8 w-8 rounded-full bg-white/80 shadow-[0_8px_32px_rgba(0,0,0,0.05)]"></div>
        </div>
    </div>

    <div class="absolute bottom-20 left-10 md:left-32 float-cloud" style="animation-delay: 2s;">
        <div class="relative h-9 w-[106px]">
            <div class="cloud-shape absolute bottom-0 left-0 h-7 w-[86px]"></div>
            <div class="absolute -top-1 left-6 h-9 w-9 rounded-full bg-white/80 shadow-[0_8px_32px_rgba(0,0,0,0.05)]"></div>
            <div class="absolute top-0 right-3 h-7 w-7 rounded-full bg-white/80 shadow-[0_8px_32px_rgba(0,0,0,0.05)]"></div>
        </div>
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

        <div class="divider">
            <span>or continue with</span>
        </div>

        <a
            href="{{ route('auth.google') }}"
            class="btn-google flex w-full items-center justify-center gap-3 rounded-lg bg-white px-4 py-3 font-semibold text-gray-700 hover:shadow-md"
        >
            <svg viewBox="0 0 48 48" class="h-5 w-5" aria-hidden="true">
                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.652 32.657 29.386 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.038l5.657-5.657C34.075 5.353 29.358 3 24 3 12.955 3 4 11.955 4 23s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.038l5.657-5.657C34.075 5.353 29.358 3 24 3 15.318 3 7.841 7.924 6.306 14.691z"/>
                <path fill="#4CAF50" d="M24 43c5.191 0 9.819-1.984 13.36-5.215l-6.172-5.225C29.134 34.317 26.715 35 24 35c-5.366 0-9.618-3.329-11.288-8.013l-6.522 5.026C7.69 39.154 15.201 43 24 43z"/>
                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-1.018 2.867-3.06 5.213-5.78 6.56l.002-.001 6.172 5.225C35.258 37.772 40 33 40 23c0-1.341-.138-2.65-.389-3.917z"/>
            </svg>
            <span>Sign in with Google</span>
        </a>

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