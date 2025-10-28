@extends('layouts.app')

@section('title', 'Detail Try Out - ' . $userTryout->tryoutPackage->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('tryouts.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Try Out
            </a>
        </div>

        <!-- Package Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $userTryout->tryoutPackage->title }}</h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $userTryout->tryoutPackage->duration_minutes }} menit
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            7 Subtes
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @if($userTryout->status === 'completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Selesai
                        </span>
                        @if($userTryout->score !== null)
                            <div class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($userTryout->score, 0) }}</div>
                            <div class="text-sm text-gray-600">Skor Total (maks: 1000)</div>
                        @endif
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sedang Berlangsung
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- LOGIC BARU: Cek Status Keseluruhan --}}
        @php
            // Hitung jumlah subtes yang completed
            $completedSubtestsCount = $userTryout->subtestProgresses->where('status', 'completed')->count();
            // Asumsi total subtes adalah 7 (bisa dibuat dinamis jika perlu)
            $isOverallCompleted = ($completedSubtestsCount >= 7); 
        @endphp

        {{-- TAMPILKAN HASIL KESELURUHAN JIKA SUDAH SELESAI --}}
        @if($isOverallCompleted)
            <div class="bg-green-50 border border-green-200 rounded-lg shadow-sm p-6 mb-8 text-center">
                <h2 class="text-xl font-semibold text-green-800 mb-2">🎉 Try Out Selesai! 🎉</h2>
                <p class="text-gray-700 mb-4">Kamu telah menyelesaikan semua subtes dalam paket try out ini.</p>
                {{-- Tampilkan skor total jika sudah dihitung (misal di kolom 'score' UserTryout) --}}
                @if($userTryout->score !== null)
                    <p class="text-sm text-gray-500">Skor Keseluruhan</p>
                    <p class="text-3xl font-bold text-green-700 mb-4">{{ number_format($userTryout->score, 0) }}</p>
                @endif
                 {{-- Tombol ke Halaman Hasil Detail --}}
                <a href="{{ route('tryout.result.overall', $userTryout->id) }}" 
                   class="inline-block px-6 py-2 rounded-md font-medium bg-green-600 hover:bg-green-700 text-white">
                    Lihat Rincian Hasil Keseluruhan
                </a>
            </div>
        @endif
        {{-- AKHIR BLOK BARU --}}

        <!-- Subtests List -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 @if($isOverallCompleted) mt-8 @endif">Daftar Subtes</h2>
            
            <div class="space-y-4">
                @foreach($userTryout->subtestProgresses as $progress)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Status Icon -->
                                <div class="flex-shrink-0">
                                    @if($progress->status === 'completed')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @elseif($progress->status === 'ongoing')
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @elseif($progress->status === 'unlocked')
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Subtest Info -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $progress->subject->name }}</h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                        <span>Subtes {{ $progress->subject->subtest_order }}</span>
                                        <span>Status: 
                                            @if($progress->status === 'completed')
                                                <span class="font-medium text-green-600">Selesai</span>
                                            @elseif($progress->status === 'ongoing')
                                                <span class="font-medium text-yellow-600">Sedang Dikerjakan</span>
                                            @elseif($progress->status === 'unlocked')
                                                <span class="font-medium text-blue-600">Belum Dikerjakan</span>
                                            @else
                                                <span class="font-medium text-gray-400">Terkunci</span>
                                            @endif
                                        </span>
                                        @if($progress->score !== null)
                                            <span class="font-medium text-gray-900">Skor: {{ number_format($progress->score, 0) }}</span>
                                        @endif
                                        @if($progress->start_time)
                                            <span>Dimulai: {{ $progress->start_time->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="flex-shrink-0">
                                @if($progress->status === 'completed')
                                    <a href="{{ route('tryout.review', $progress->id) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-500 hover:bg-purple-600 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Lihat Pembahasan
                                    </a>
                                @elseif($progress->status === 'ongoing')
                                    <a href="{{ route('tryout.conduct', $progress) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Lanjutkan
                                    </a>
                                @elseif($progress->status === 'unlocked')
                                    <a href="{{ route('tryout.conduct', $progress) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mulai Subtes
                                    </a>
                                @else
                                    <button disabled 
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Terkunci
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
            <h3 class="text-lg font-medium text-blue-900 mb-2">Petunjuk Pengerjaan</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Kerjakan subtes secara berurutan dari 1 hingga 7</li>
                <li>• Setiap subtes memiliki waktu pengerjaan yang terbatas</li>
                <li>• Subtes berikutnya akan terbuka setelah menyelesaikan subtes sebelumnya</li>
                <li>• Anda dapat melihat hasil setiap subtes yang telah diselesaikan</li>
            </ul>
        </div>
    </div>
</div>
@endsection