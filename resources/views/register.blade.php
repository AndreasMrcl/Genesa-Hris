<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
    @include('layout.head')
    <style>
        .strength-bar {
            height: 8px;
            background-color: #ddd;
            border-radius: 4px;
            margin-top: 5px;
        }

        .strength-bar span {
            display: block;
            height: 100%;
            border-radius: 4px;
        }

        .strength-weak {
            width: 33%;
            background-color: red;
        }

        .strength-medium {
            width: 66%;
            background-color: orange;
        }

        .strength-strong {
            width: 100%;
            background-color: green;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #555;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        @media (max-width: 375px) {
            .h-screen {
                height: auto;
            }
        }
    </style>
</head>

<body class="">

    <div class="h-screen w-full mx-auto bg-linear-to-b from-red-800 to-gray-100 flex items-center p-4 xl:p-0 2xl:p-0">
        <div class="mx-auto sm:max-w-sm w-full space-y-3 p-8 bg-white rounded-xl">
            <div class="space-y-3">
                <div>
                    <h1 class="text-5xl xl:text-6xl 2xl:text-6xl font-extrabold text-black">Beil</h1>
                </div>
                <div>
                    <h1 class="text-2xl xl:text-3xl 2xl:text-3xl font-extrabold text-black">Register</h1>
                    <p class="text-black text-lg xl:text-xl 2xl:text-xl font-extralight">Sign up</p>
                </div>
            </div>
            <form method="post" action="{{ route('signup') }}" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="name" class="text-black">Username</label>
                    <input class="w-full p-2 bg-gray-100 rounded-xl" type="text" name="name" required />
                </div>
                <div class="space-y-2">
                    <label for="email" class="text-black">Email</label>
                    <input class="w-full p-2 bg-gray-100 rounded-xl" type="email" name="email" required />
                </div>
                <div class='space-y-2'>
                    <label for="password" class='text-black'>Password</label>
                    <div class="password-container">
                        <input id="password" class='w-full p-2 bg-gray-100 rounded-xl pr-12' type="password"
                            name="password" required />
                        <i id="toggle-password" class="fas fa-eye toggle-password"></i>
                    </div>
                    <div class="strength-bar" id="strength-bar">
                        <span></span>
                    </div>
                    <div class="password-requirements" id="password-requirements">
                        Password must be at least 8 characters long, contain an uppercase letter, a number, and a
                        special character.
                    </div>
                </div>
                <div class='space-y-2'>
                    <label for="password_confirmation" class='text-black'>Password Confirmation</label>
                    <div class="password-container">
                        <input id="password_confirmation" class='w-full p-2 bg-gray-100 rounded-xl pr-12'
                            type="password" name="password_confirmation" required />
                        <i id="toggle-password-confirmation" class="fas fa-eye toggle-password"></i>
                    </div>
                </div>
                <div class="flex justify-between">
                    <div>
                        <h1 class="text-black text-base xl:text-lg font-extralight">Already Have Account?</h1>
                    </div>
                    <div>
                        <a href="{{ route('login') }}">
                            <h1
                                class="text-black text-base xl:text-lg font-extralight underline hover:font-semibold transition-all delay-100">
                                Sign In</h1>
                        </a>
                    </div>
                </div>
                <div
                    class='border-4 border-red-700 p-2 rounded-3xl w-3/5 mx-auto hover:scale-110 duration-200 transition-all'>
                    <button name="submit" type="submit"
                        class='flex mx-auto text-black text-xl xl:text-2xl 2xl:text-2xl font-semibold'>
                        Sign Up
                    </button>
                </div>
            </form>
        </div>
    </div>
    @include('sweetalert::alert')


    <script>
        // Password Visibility Toggle
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggle-password-confirmation').addEventListener('click', function() {
            const passwordConfirmationField = document.getElementById('password_confirmation');
            const type = passwordConfirmationField.type === 'password' ? 'text' : 'password';
            passwordConfirmationField.type = type;

            this.classList.toggle('fa-eye-slash');
        });

        // Password Strength Checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength-bar');
        const requirements = document.getElementById('password-requirements');

        passwordInput.addEventListener('input', () => {
            const value = passwordInput.value;
            let strength = 0;

            if (value.length >= 8) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[\W_]/.test(value)) strength++;

            const strengthClasses = ['strength-weak', 'strength-medium', 'strength-strong'];
            strengthBar.querySelector('span').className = strengthClasses[strength - 1] || 'strength-weak';
            requirements.style.color = strength < 4 ? 'red' : 'green';
        });
    </script>
</body>

</html>
