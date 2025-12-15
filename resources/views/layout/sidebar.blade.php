<div class="flex">
    <aside id="sidebar"
        class="font-poppins fixed inset-y-0 my-6 ml-4 w-full max-w-72 md:max-w-60 xl:max-w-64 2xl:max-w-64 z-50 rounded-lg bg-white overflow-y-scroll transform transition-transform duration-300 -translate-x-full md:translate-x-0 ease-in-out shadow-xl">
        <div class="p-2">
            <div class="p-4">
                <a class="" href="{{ route('dashboard') }}">
                    <!-- Logo + Title -->
                    <div class="">
                        <img class="w-32 h-fit mx-auto" src="{{ asset('logo.png') }}" alt="Logo">
                    </div>
                </a>
            </div>
            <hr class="mx-5 shadow-2xl text-gray-100 rounded-r-xl rounded-l-xl" />
            <div>
                <ul class="">
                    <li class="p-4 mx-2">
                        <a class="" href="{{ route('dashboard') }}">
                            <div class="flex space-x-4">
                                <div class="bg-sky-600 p-2 rounded-xl">
                                    <i class="material-icons text-white">home</i>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Dashboard</h1>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons text-white">dataset</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-black text-base font-normal">Department</h1>
                            </div>
                        </div>
                    </li>
                    <hr class="mx-5 shadow-2xl text-gray-100 rounded-r-xl rounded-l-xl" />

                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('branch') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Branch</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('position') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Position</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('shift') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Shift</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('announcement') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Announcement</h1>
                            </a>
                        </div>
                    </li>


                    <li class="p-4 mx-2">
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons text-white">group</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-black text-base font-normal">People</h1>
                            </div>
                        </div>
                    </li>
                    <hr class="mx-5 shadow-2xl text-gray-100 rounded-r-xl rounded-l-xl" />

                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('employee') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Employee</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('attendance') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Attendace</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('overtime') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Overtime</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('leave') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Leave</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('note') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Note</h1>
                            </a>
                        </div>
                    </li>

                    <li class="p-4 mx-2">
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons text-white">payments</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-black text-base font-normal">Compensation</h1>
                            </div>
                        </div>
                    </li>

                    <hr class="mx-5 shadow-2xl text-gray-100 rounded-r-xl rounded-l-xl" />

                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('allowance') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Allowance</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('deduction') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Deduction</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <div class="ml-16 md:ml-14">
                            <a href="{{ route('payroll') }}">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">Payroll</h1>
                            </a>
                        </div>
                    </li>
                    <li class="p-4 mx-2">
                        <a class="" href="{{ route('activityLog') }}">
                            <div class="flex space-x-4">
                                <div class="bg-sky-600 p-2 rounded-xl">
                                    <i class="material-icons text-white">history</i>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Activity Logs</h1>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="p-4 mx-2">
                        <a class="" href="{{ route('companyConfig') }}">
                            <div class="flex space-x-4">
                                <div class="bg-sky-600 p-2 rounded-xl">
                                    <i class="material-icons text-white">settings</i>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Setting</h1>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li class="p-4 mx-2">
                        <form class="" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <div class="flex space-x-4">
                                <div class="bg-sky-600 p-2 rounded-xl">
                                    <i class="material-icons font-extrabold rotate-180 text-white">logout</i>
                                </div>
                                <button class="text-gray-500 hover:text-black text-base font-normal" type="submit">
                                    Logout
                                </button>
                            </div>
                        </form>
                    </li>

                </ul>
            </div>
        </div>
    </aside>
</div>