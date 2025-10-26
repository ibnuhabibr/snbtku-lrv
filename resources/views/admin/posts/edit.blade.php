@extends('layouts.app')

@section('title', 'Edit Artikel - Admin SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('admin.posts.index') }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Edit Artikel</h1>
            </div>
            <p class="text-gray-600">Edit artikel: {{ $post->title }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.posts.update', $post) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Artikel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $post->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                           placeholder="Contoh: Tips Mengerjakan Soal TPS dengan Efektif"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="mb-6">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug', $post->slug) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-500 @enderror"
                           placeholder="tips-mengerjakan-soal-tps-dengan-efektif"
                           readonly>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Slug akan dibuat otomatis dari judul</p>
                </div>

                <!-- Body -->
                <div class="mb-6">
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                        Konten Artikel <span class="text-red-500">*</span>
                    </label>
                    <textarea id="body" 
                              name="body" 
                              rows="20"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('body') border-red-500 @enderror"
                              placeholder="Tulis konten artikel di sini... Anda bisa menggunakan HTML untuk formatting."
                              required>{{ old('body', $post->body) }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Anda bisa menggunakan HTML untuk formatting. Contoh: &lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;
                    </p>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Draft: Belum bisa dilihat publik | Published: Bisa dilihat publik</p>
                </div>

                <!-- Article Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Artikel</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Penulis:</span>
                            <span class="text-gray-900">{{ $post->user->name }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Dibuat:</span>
                            <span class="text-gray-900">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Terakhir diupdate:</span>
                            <span class="text-gray-900">{{ $post->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Status saat ini:</span>
                            @if($post->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Preview</h3>
                    <div id="preview" class="prose max-w-none">
                        <p class="text-gray-500">Preview akan muncul di sini saat Anda mengetik...</p>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.posts.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                        Update Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const bodyTextarea = document.getElementById('body');
    const preview = document.getElementById('preview');

    function generateSlug(text) {
        return text
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
            .trim('-'); // Remove leading/trailing hyphens
    }

    function updatePreview() {
        const title = titleInput.value;
        const body = bodyTextarea.value;
        
        if (title || body) {
            let previewHtml = '';
            if (title) {
                previewHtml += `<h1 class="text-2xl font-bold mb-4">${title}</h1>`;
            }
            if (body) {
                previewHtml += body;
            }
            preview.innerHTML = previewHtml;
        } else {
            preview.innerHTML = '<p class="text-gray-500">Preview akan muncul di sini saat Anda mengetik...</p>';
        }
    }

    titleInput.addEventListener('input', function() {
        const slug = generateSlug(this.value);
        slugInput.value = slug;
        updatePreview();
    });

    bodyTextarea.addEventListener('input', updatePreview);

    // Initial preview update
    updatePreview();
});
</script>
@endsection