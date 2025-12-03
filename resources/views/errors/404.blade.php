<!DOCTYPE html>
<html lang="en">

<head>
    <title>404 - Page Not Found</title>
    @include('layout.head')
</head>

<body class="bg-gradient-to-b from-sky-800 to-gray-100 h-screen grid grid-cols-1">
    <div class="my-auto text-center space-y-4">
        <div class="space-y-2">
            <h1 class="text-4xl font-bold text-white">404</h1>
            <h1 class="text-2xl font-semibold text-white">Page Not Found</h1>
            <h1 class="text-white">Oops! The page you're looking for doesn’t exist.</h1>
        </div>
        <div class="p-2 bg-sky-600 hover:bg-sky-800 rounded-md w-fit mx-auto transition-all delay-100 hover:scale-105">
            <a href="{{ route('login') }}" class="">
                <h1 class="text-lg font-normal text-white hover:underline transition-all delay-100 px-4">
                    Return to Home
                </h1>
            </a>
        </div>
    </div>
</body>

</html>
