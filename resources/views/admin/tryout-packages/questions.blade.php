@extends('layouts.app')

@section('title', 'Kelola Soal - ' . $tryoutPackage->title . ' - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('admin.tryout-packages.show', $tryoutPackage) }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Soal</h1>
                    <p class="text-gray-600">{{ $tryoutPackage->title }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @if($tryoutPackage->status === 'published')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Published
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        Draft
                    </span>
                @endif
                <span class="text-gray-600">{{ $tryoutPackage->questions->count() }} soal</span>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add Questions Section -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Tambah Soal ke Paket</h3>
            </div>
            <div class="px-6 py-4">
                <form action="{{ route('admin.tryout-packages.questions.add', $tryoutPackage) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Subject Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Mata Pelajaran
                            </label>
                            <select id="subject_id" 
                                    name="subject_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="topic_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Topik
                            </label>
                            <select id="topic_id" 
                                    name="topic_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Topik</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="button" 
                                    id="filterQuestions"
                                    class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium">
                                Filter Soal
                            </button>
                        </div>
                    </div>

                    <!-- Available Questions -->
                    <div id="availableQuestions" class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Soal yang Tersedia</h4>
                        <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-md">
                            <div id="questionsList" class="p-4">
                                <p class="text-gray-500 text-center">Pilih mata pelajaran dan topik untuk melihat soal yang tersedia</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                id="addSelectedQuestions"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                                style="display: none;">
                            Tambah Soal Terpilih
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Questions in Package -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Soal dalam Paket ({{ $tryoutPackage->questions->count() }})</h3>
            </div>
            
            @if($tryoutPackage->questions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Urut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Soal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Pelajaran
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Topik
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tryoutPackage->questions->sortBy('pivot.order') as $question)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $question->pivot->order }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ Str::limit(strip_tags($question->question_text), 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $question->topic->subject->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $question->topic->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.questions.show', $question) }}" 
                                               class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            <form action="{{ route('admin.tryout-packages.questions.remove', [$tryoutPackage, $question]) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus soal ini dari paket?')">
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada soal dalam paket</h3>
                    <p class="mt-1 text-sm text-gray-500">Tambahkan soal untuk membuat paket try out yang lengkap.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subjectSelect = document.getElementById('subject_id');
    const topicSelect = document.getElementById('topic_id');
    const filterButton = document.getElementById('filterQuestions');
    const questionsList = document.getElementById('questionsList');
    const addButton = document.getElementById('addSelectedQuestions');

    // Topics data
    const topics = @json($topics);

    // Update topics when subject changes
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        topicSelect.innerHTML = '<option value="">Semua Topik</option>';
        
        if (subjectId) {
            const subjectTopics = topics.filter(topic => topic.subject_id == subjectId);
            subjectTopics.forEach(topic => {
                const option = document.createElement('option');
                option.value = topic.id;
                option.textContent = topic.name;
                topicSelect.appendChild(option);
            });
        }
    });

    // Filter questions
    filterButton.addEventListener('click', function() {
        const subjectId = subjectSelect.value;
        const topicId = topicSelect.value;
        
        fetch(`{{ route('admin.tryout-packages.questions.available', $tryoutPackage) }}?subject_id=${subjectId}&topic_id=${topicId}`)
            .then(response => response.json())
            .then(data => {
                if (data.questions.length > 0) {
                    let html = '';
                    data.questions.forEach(question => {
                        html += `
                            <div class="border-b border-gray-200 py-3 last:border-b-0">
                                <label class="flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" name="question_ids[]" value="${question.id}" class="mt-1">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">${question.topic.subject.name} - ${question.topic.name}</div>
                                        <div class="text-sm text-gray-600 mt-1">${question.question_text.substring(0, 150)}...</div>
                                    </div>
                                </label>
                            </div>
                        `;
                    });
                    questionsList.innerHTML = html;
                    addButton.style.display = 'block';
                } else {
                    questionsList.innerHTML = '<p class="text-gray-500 text-center">Tidak ada soal yang tersedia untuk filter ini</p>';
                    addButton.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                questionsList.innerHTML = '<p class="text-red-500 text-center">Terjadi kesalahan saat memuat soal</p>';
            });
    });
});
</script>
@endsection