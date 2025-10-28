{{-- resources/views/tryouts/overall-result.blade.php --}}
@extends('layouts.app')

@section('title', 'Hasil Keseluruhan: ' . $userTryout->tryoutPackage->title . ' - SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header dengan Overall Score --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-8 mb-8 text-white">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-2">{{ $tryoutPackage->title }}</h1>
                <p class="text-emerald-100 mb-6">Hasil Keseluruhan Try Out</p>
                <div class="bg-white bg-opacity-20 rounded-lg p-6 inline-block">
                    <div class="text-sm text-emerald-100 mb-2">Skor Keseluruhan</div>
                    <div class="text-5xl font-bold">{{ number_format($overallScore, 0) }}</div>
                </div>
            </div>
        </div>

        {{-- Statistik Ringkasan --}}
        <div class="bg-white rounded-lg shadow-md border p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Statistik Ringkasan</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-slate-700">{{ $totalOverallQuestions }}</div>
                    <div class="text-sm text-slate-600">Total Soal</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-emerald-700">{{ $totalOverallCorrect }}</div>
                    <div class="text-sm text-emerald-600">Benar</div>
                </div>
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-700">{{ $totalIncorrect }}</div>
                    <div class="text-sm text-red-600">Salah</div>
                </div>
                <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-amber-700">{{ $totalUnanswered }}</div>
                    <div class="text-sm text-amber-600">Tidak Dijawab</div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow-md border mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Rincian Skor per Subtes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-emerald-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-emerald-700 uppercase tracking-wider">Subtes</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-emerald-700 uppercase tracking-wider">Skor</th> {{-- Format: Benar : Total --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-emerald-700 uppercase tracking-wider">Durasi</th> {{-- Durasi Pengerjaan Subtes --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-emerald-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-emerald-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subtestDetails as $index => $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center text-emerald-700">
                                    {{ $detail['score'] !== null ? number_format($detail['score'], 0) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $detail['duration'] !== null ? $detail['duration'] . ' menit' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        {{ $detail['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <a href="{{ $detail['review_link'] }}" class="text-emerald-600 hover:text-emerald-900 font-medium">
                                        Lihat Pembahasan
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ringkasan Waktu --}}
        <div class="bg-white rounded-lg shadow-md border mb-8 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Waktu Pengerjaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                    <div class="text-sm text-emerald-600 font-medium">Total Waktu</div>
                    <div class="text-2xl font-bold text-emerald-900">
                        {{ $totalDurationMinutes !== null ? round($totalDurationMinutes, 1) . ' menit' : '-' }}
                    </div>
                </div>
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                    <div class="text-sm text-teal-600 font-medium">Rata-rata per Subtes</div>
                    <div class="text-2xl font-bold text-teal-900">
                        {{ $totalDurationMinutes !== null && count($subtestDetails) > 0 ? round($totalDurationMinutes / count($subtestDetails), 1) . ' menit' : '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('tryout.detail', $userTryout->id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Detail Tryout
            </a>
            <a href="{{ route('tryouts.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                </svg>
                Lihat Semua Tryout
            </a>
        </div>
    </div>
</div>
@endsection