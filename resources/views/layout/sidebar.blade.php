<div class="flex">
    <aside id="sidebar"
        class="font-poppins fixed inset-y-0 my-6 ml-4 w-full max-w-72 md:max-w-60 xl:max-w-64 2xl:max-w-64 z-50 rounded-lg bg-white overflow-y-auto transform transition-transform duration-300 -translate-x-full md:translate-x-0 ease-in-out shadow-xl">
        <div class="p-2">
            <div class="p-4">
                <a href="{{ route('dashboard') }}">
                    <div>
                        <img class="w-32 h-fit mx-auto" src="{{ asset('logo.png') }}" alt="Logo">
                    </div>
                </a>
            </div>

            <hr class="mx-5 shadow-2xl text-gray-100 rounded-xl" />

            <ul>
                <!-- Dashboard -->
                <li class="p-4 mx-2">
                    <a href="{{ route('dashboard') }}">
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons text-white">home</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">
                                    Dashboard
                                </h1>
                            </div>
                        </div>
                    </a>
                </li>

                <!-- Department -->
                <li class="p-4 mx-2">
                    <div class="flex space-x-4">
                        <div class="bg-sky-600 p-2 rounded-xl">
                            <i class="material-icons text-white">dataset</i>
                        </div>
                        <div class="my-auto">
                            <h1 class="text-black text-base font-normal">
                                Departemen
                            </h1>
                        </div>
                    </div>
                </li>

                <hr class="mx-5 shadow-2xl text-gray-100 rounded-xl" />

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('branch') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Cabang
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('position') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Jabatan
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('schedule') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Jadwal
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('announcement') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Pengumuman
                        </h1>
                    </a>
                </li>

                <!-- People -->
                <li class="p-4 mx-2">
                    <div class="flex space-x-4">
                        <div class="bg-sky-600 p-2 rounded-xl">
                            <i class="material-icons text-white">group</i>
                        </div>
                        <div class="my-auto">
                            <h1 class="text-black text-base font-normal">
                                Karyawan
                            </h1>
                        </div>
                    </div>
                </li>

                <hr class="mx-5 shadow-2xl text-gray-100 rounded-xl" />

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('employee') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Data Karyawan
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('attendance') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Absensi
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('overtime') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Lembur
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('leave') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Cuti
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('note') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Catatan
                        </h1>
                    </a>
                </li>

                <!-- Compensation -->
                <li class="p-4 mx-2">
                    <div class="flex space-x-4">
                        <div class="bg-sky-600 p-2 rounded-xl">
                            <i class="material-icons text-white">payments</i>
                        </div>
                        <div class="my-auto">
                            <h1 class="text-black text-base font-normal">
                                Kompensasi
                            </h1>
                        </div>
                    </div>
                </li>

                <hr class="mx-5 shadow-2xl text-gray-100 rounded-xl" />

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('allowance') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Tunjangan
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('deduction') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Potongan
                        </h1>
                    </a>
                </li>

                <li class="p-4 mx-2 ml-16 md:ml-14">
                    <a href="{{ route('payroll') }}">
                        <h1 class="text-gray-500 hover:text-black text-base font-normal">
                            Penggajian
                        </h1>
                    </a>
                </li>

                <!-- Logs -->
                <li class="p-4 mx-2">
                    <a href="{{ route('activityLog') }}">
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons text-white">history</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">
                                    Log Aktivitas
                                </h1>
                            </div>
                        </div>
                    </a>
                </li>

                <!-- Setting -->
                <li class="p-4 mx-2">
                    <a href="{{ route('companyConfig') }}">
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons text-white">settings</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-gray-500 hover:text-black text-base font-normal">
                                    Pengaturan
                                </h1>
                            </div>
                        </div>
                    </a>
                </li>

                <!-- Logout -->
                <li class="p-4 mx-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <div class="flex space-x-4">
                            <div class="bg-sky-600 p-2 rounded-xl">
                                <i class="material-icons rotate-180 text-white">logout</i>
                            </div>
                            <button class="text-gray-500 hover:text-black text-base font-normal" type="submit">
                                Keluar
                            </button>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </aside>
</div>
