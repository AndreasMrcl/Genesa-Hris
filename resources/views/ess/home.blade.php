<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home</title>
    @include('ess.layout.head')
    <style>
        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .slider-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
            gap: 20px;
        }

        .slider-slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .slider-slide img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Automatic sliding effect using keyframes */
        @keyframes slideAnimation {
            0% {
                transform: translateX(0);
            }

            33% {
                transform: translateX(-100%);
            }

            66% {
                transform: translateX(-200%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .slider-wrapper {
            animation: slideAnimation 10s infinite;
        }
    </style>
</head>

<body class="font-poppins bg-gray-50 w-full md:max-w-sm mx-auto  ">
    <div class=''>
        <div class="space-y-2">
            <div class="grid grid-cols-4 bg-sky-800 p-4 shadow-xl rounded-b-[20px]">
                <div class="space-y-2 col-span-3">
                    <div class="my-auto">
                        <h1 class="text-2xl text-white font-bold">{{ $userCompany->company }}</h1>
                    </div>
                    <div>
                        <div class="my-auto">
                            <h1 class="text-sm text-white font-light line-clamp-2">{{ $userCompany->location }}
                            </h1>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="space-y-2">
                            <h1 class="font-base text-white text-sm">Hi, {{ auth()->user()->name }}</h1>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-end my-auto ">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <div class="my-auto bg-white p-1.5 rounded-md">
                            <button type="submit">
                                <i class="material-icons font-extrabold rotate-180 text-black">logout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="p-4 space-y-6">

                <div class="grid grid-cols-3 gap-6">
                </div>

                <div class="grid grid-cols-2 gap-2">

                </div>

            </div>

        </div>
    </div>
    <!-- ✅ Footer -->
    <div class="p-4 ">
        <div class="">
            <h1 class="text-black text-sm my-auto">Powered by</h1>
        </div>
    </div>

</body>

</html>
