<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-blue-100">Siap untuk menghadapi SNBT? Mari mulai persiapanmu dengan try out dan artikel terbaru.</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @php
                    $totalTryouts = \App\Models\UserTryout::where('user_id', Auth::id())->count();
                    $completedTryouts = \App\Models\UserTryout::where('user_id', Auth::id())->where('status', 'completed')->count();
                    $ongoingTryouts = \App\Models\UserTryout::where('user_id', Auth::id())->where('status', 'ongoing')->count();
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalTryouts }}</p>
                                <p class="text-gray-600">Total Try Out</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 mr-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $completedTryouts }}</p>
                                <p class="text-gray-600">Try Out Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 mr-4">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $ongoingTryouts }}</p>
                                <p class="text-gray-600">Try Out Berlangsung</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Try Out Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Try Out SNBT</h4>
                        <p class="text-gray-600 mb-4">Uji kemampuanmu dengan simulasi ujian SNBT yang realistis.</p>
                        <a href="{{ route('tryouts.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Mulai Try Out
                        </a>
                    </div>
                </div>

                <!-- Articles Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Artikel & Tips</h4>
                        <p class="text-gray-600 mb-4">Baca tips dan strategi terbaik untuk menghadapi SNBT.</p>
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Baca Artikel
                        </a>
                    </div>
                </div>
            </div>

            @if($ongoingTryouts > 0)
                <!-- Ongoing Tryouts -->
                <div class="mt-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Try Out yang Sedang Berlangsung</h4>
                            @php
                                $ongoing = \App\Models\UserTryout::where('user_id', Auth::id())
                                    ->where('status', 'ongoing')
                                    ->with('tryoutPackage')
                                    ->get();
                            @endphp
                            <div class="space-y-3">
                                @foreach($ongoing as $userTryout)
                                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <div>
                                            <h5 class="font-medium text-gray-900">{{ $userTryout->tryoutPackage->title }}</h5>
                                            <p class="text-sm text-gray-600">Dimulai: {{ $userTryout->start_time->format('d M Y, H:i') }}</p>
                                        </div>
                                        <a href="{{ route('tryout.conduct', $userTryout->id) }}" class="inline-flex items-center px-3 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition ease-in-out duration-150">
                                            Lanjutkan
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
