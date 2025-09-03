<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Absensi NotePro</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-white min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 rounded-lg shadow-lg border border-gray-100" style="background: #fff;">
        <div class="flex flex-col items-center mb-6">
            <div class="text-3xl font-extrabold mb-2" style="color: #ff040c;">Absensi <span style="color:#fb0302;">NotePro</span></div>
            <div class="text-gray-400 text-xs">Silakan login untuk melanjutkan</div>
        </div>
        @if ($errors->any())
            <div class="mb-4 text-sm text-white bg-red-500 rounded p-2">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="email">Email</label>
                <input id="email" type="email" name="email" required autofocus class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#fb0302]">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2" for="password">Password</label>
                <input id="password" type="password" name="password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#fb0302]">
            </div>
            <button type="submit" class="w-full py-3 rounded font-bold text-white text-lg" style="background: linear-gradient(90deg, #ff040c 0%, #fb0302 100%);">Login</button>
        </form>
    </div>
</body>
</html>
