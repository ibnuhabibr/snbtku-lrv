<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-12 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Paket Try Out SNBT</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Pilih paket try out untuk memulai latihan soal SNBT dan ukur kemampuanmu</p>
    </div>

    @if($packages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($packages as $package)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $package->title }}</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-gray-600">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ $package->duration_minutes }} menit</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ $package->questions_count }} soal</span>
                            </div>
                        </div>

                        @auth
                            @if($package->user_status === 'not_started')
                                <button 
                                    wire:click="viewPackageDetails({{ $package->id }})"
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                >
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mulai Kerjakan
                                    </span>
                                </button>
                            @elseif($package->user_status === 'ongoing')
                                <button 
                                    wire:click="viewPackageDetails({{ $package->id }})"
                                    class="w-full bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                >
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Lanjutkan
                                    </span>
                                </button>
                            @elseif($package->user_status === 'completed')
                                <div class="space-y-2">
                                    <div class="text-sm text-green-600 font-medium">
                                        ✓ Selesai - Skor: {{ number_format($package->score, 0) }}
                                    </div>
                                    <button 
                                        wire:click="viewPackageDetails({{ $package->id }})"
                                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                                    >
                                        <span class="flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Lihat Hasil
                                        </span>
                                    </button>
                                </div>
                            @endif
                        @else
                            <a 
                                href="{{ route('login') }}"
                                class="block w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold py-4 px-6 rounded-xl text-center transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                            >
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Login untuk Mulai
                                </span>
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <div class="mx-auto h-24 w-24 text-gray-300 mb-6">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum ada paket try out</h3>
            <p class="text-lg text-gray-500 max-w-md mx-auto">Paket try out akan segera tersedia. Pantau terus untuk mendapatkan latihan soal SNBT terbaru.</p>
        </div>
    @endif
</div>
