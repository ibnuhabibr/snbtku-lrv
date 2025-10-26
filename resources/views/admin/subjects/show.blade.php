@extends('layouts.app')

@section('title', 'Detail Mata Pelajaran: ' . $subject->name . ' - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $subject->name }}</h1>
                <p class="text-gray-600">Detail mata pelajaran dan topik-topiknya</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.subjects.edit', $subject) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Edit Mata Pelajaran
                </a>
                <a href="{{ route('admin.subjects.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Subject Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Mata Pelajaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $subject->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Slug</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $subject->slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Topik</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $subject->topics->count() }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Soal</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $subject->topics->sum(function($topic) { return $topic->questions->count(); }) }}</p>
                </div>
            </div>
        </div>

        <!-- Topics -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Topik dalam Mata Pelajaran Ini</h3>
                <a href="{{ route('admin.topics.create', ['subject_id' => $subject->id]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                    Tambah Topik
                </a>
            </div>
            
            @if($subject->topics->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Topik
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Slug
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Soal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subject->topics as $topic)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $topic->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $topic->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $topic->questions->count() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.topics.show', $topic) }}" 
                                               class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            <a href="{{ route('admin.topics.edit', $topic) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('admin.topics.destroy', $topic) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus topik ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada topik</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan topik pertama untuk mata pelajaran ini.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.topics.create', ['subject_id' => $subject->id]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Tambah Topik Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection