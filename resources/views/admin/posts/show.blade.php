@extends('layouts.app')

@section('title', $post->title . ' - Admin SNBTKU')

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
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $post->title }}</h1>
                    <div class="flex items-center space-x-4 mt-2">
                        @if($post->status === 'published')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Draft
                            </span>
                        @endif
                        <span class="text-gray-600">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-8 flex flex-wrap gap-4">
            <a href="{{ route('admin.posts.edit', $post) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                Edit Artikel
            </a>
            @if($post->status === 'published')
                <a href="#" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">
                    Lihat di Frontend
                </a>
            @endif
            <form action="{{ route('admin.posts.destroy', $post) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium">
                    Hapus Artikel
                </button>
            </form>
        </div>

        <!-- Article Meta -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Artikel</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Judul</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $post->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $post->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Penulis</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $post->user->name }}
                            <span class="text-gray-500">({{ $post->user->email }})</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Draft
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $post->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terakhir diupdate</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $post->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Jumlah Karakter</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ strlen(strip_tags($post->body)) }} karakter 
                            <span class="text-gray-500">(~{{ ceil(strlen(strip_tags($post->body)) / 200) }} menit baca)</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Article Content -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Konten Artikel</h3>
            </div>
            <div class="px-6 py-6">
                <article class="prose max-w-none">
                    {!! $post->body !!}
                </article>
            </div>
        </div>

        <!-- Raw HTML View (for debugging) -->
        <div class="bg-white rounded-lg shadow mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">HTML Raw (untuk debugging)</h3>
            </div>
            <div class="px-6 py-4">
                <pre class="bg-gray-100 p-4 rounded-md text-sm overflow-x-auto"><code>{{ $post->body }}</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection