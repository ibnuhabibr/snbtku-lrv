{{-- resources/views/bank-soal/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Bank Soal Latihan - SNBTKU')

@section('content')
<div class="py-12 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 px-4 sm:px-0 text-center">🏦 Bank Soal SNBTKU</h1>
        <p class="text-center text-gray-600 mb-10 -mt-4">Pilih subtes untuk memulai latihan soal per topik.</p>

        @if($subjects->isEmpty())
            <p class="text-center text-gray-500">Belum ada subtes yang tersedia.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @php
                    $tpsSubjects = $subjects->whereIn('subtest_order', [1, 2, 3, 4])->sortBy('subtest_order'); 
                    $literasiSubjects = $subjects->whereIn('subtest_order', [5, 6, 7])->sortBy('subtest_order');
                @endphp

                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transform hover:scale-[1.02] transition duration-300">
                    <h2 class="text-xl font-semibold text-center text-blue-700 mb-6 border-b pb-3">Tes Potensi Skolastik (TPS)</h2>
                    <div class="space-y-4">
                        @forelse($tpsSubjects as $subject)
                            <a href="{{ route('bank-soal.subject.show', $subject->slug) }}" 
                               class="block w-full text-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-blue-800 font-medium transition duration-150 shadow-sm hover:shadow-md">
                                {{ $subject->name }}
                            </a>
                        @empty <p class="text-sm text-gray-500 text-center">Subtes TPS belum tersedia.</p> @endforelse
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transform hover:scale-[1.02] transition duration-300">
                     <h2 class="text-xl font-semibold text-center text-indigo-700 mb-6 border-b pb-3">Tes Literasi & Penalaran Matematika</h2>
                     <div class="space-y-4">
                        @forelse($literasiSubjects as $subject)
                            <a href="{{ route('bank-soal.subject.show', $subject->slug) }}"
                               class="block w-full text-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg text-indigo-800 font-medium transition duration-150 shadow-sm hover:shadow-md">
                                {{ $subject->name }}
                            </a>
                        @empty <p class="text-sm text-gray-500 text-center">Subtes Literasi & Penalaran belum tersedia.</p> @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection