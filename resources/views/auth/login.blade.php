<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Absensi NotePro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 rounded-2xl shadow-2xl border border-gray-200 bg-white">
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-[#ff040c] rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-clock text-white text-2xl"></i>
            </div>
            <div class="text-3xl font-extrabold mb-2" style="color: #ff040c;">Absensi <span style="color:#fb0302;">NotePro</span></div>
            <div class="text-gray-500 text-sm">Silakan login untuk melanjutkan</div>
        </div>
        @if ($errors->any())
            <div class="mb-4 text-sm text-white bg-red-500 rounded p-2">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ url('/') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 mb-3 font-medium" for="email">
                    <i class="fas fa-envelope mr-2 text-[#ff040c]"></i>Email
                </label>
                <input id="email" type="email" name="email" required autofocus 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] focus:border-transparent transition-all duration-200"
                       placeholder="Masukkan email Anda">
            </div>
            <div class="mb-8">
                <label class="block text-gray-700 mb-3 font-medium" for="password">
                    <i class="fas fa-lock mr-2 text-[#ff040c]"></i>Password
                </label>
                <input id="password" type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff040c] focus:border-transparent transition-all duration-200"
                       placeholder="Masukkan password Anda">
            </div>
            <button type="submit" class="w-full py-4 rounded-lg font-bold text-white text-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105" 
                    style="background: linear-gradient(135deg, #ff040c 0%, #fb0302 100%);">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </button>
        </form>
    </div>
</body>
</html>
