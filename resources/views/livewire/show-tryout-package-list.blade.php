<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Paket Try Out SNBT</h1>
        <p class="mt-2 text-gray-600">Pilih paket try out untuk memulai latihan soal SNBT</p>
    </div>

    @if($packages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $package)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $package->title }}</h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $package->duration_minutes }} menit
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ $package->total_questions }} soal
                            </div>
                        </div>

                        @auth
                            @php
                                $userTryout = $userTryouts->get($package->id);
                            @endphp

                            @if($userTryout)
                                @if($userTryout->status === 'ongoing')
                                    <button 
                                        wire:click="startTryout({{ $package->id }})"
                                        class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                                    >
                                        Lanjutkan Try Out
                                    </button>
                                @elseif($userTryout->status === 'completed')
                                    <div class="space-y-2">
                                        <div class="text-sm text-green-600 font-medium">
                                            ✓ Selesai - Skor: {{ number_format($userTryout->score, 1) }}
                                        </div>
                                        <button 
                                            wire:click="startTryout({{ $package->id }})"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                                        >
                                            Lihat Hasil
                                        </button>
                                    </div>
                                @endif
                            @else
                                <button 
                                    wire:click="startTryout({{ $package->id }})"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                                >
                                    Mulai Try Out
                                </button>
                            @endif
                        @else
                            <a 
                                href="{{ route('login') }}"
                                class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md text-center transition duration-150 ease-in-out"
                            >
                                Login untuk Mulai
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada paket try out</h3>
            <p class="mt-1 text-sm text-gray-500">Paket try out akan segera tersedia.</p>
        </div>
    @endif
</div>
