{{-- resources/views/bank-soal/show-subject.blade.php --}}
@extends('layouts.app')
@section('title', 'Latihan: ' . $subject->name . ' - SNBTKU')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">

    {{-- Mobile Hamburger Button --}}
    <div class="md:hidden bg-white border-b shadow-sm p-4 flex justify-between items-center">
        <div>
            <a href="{{ route('bank-soal.index') }}" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali ke Bank Soal</a>
            <h3 class="font-bold text-lg text-gray-800">{{ $subject->name }}</h3>
        </div>
        <button @click="sidebarOpen = true" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden"
         style="display: none;">
    </div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-80 bg-white border-r shadow-lg transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:w-4/12 lg:w-3/12 xl:w-2/12 md:sticky md:top-0 md:h-screen overflow-y-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           x-show="sidebarOpen || window.innerWidth >= 768"
           x-transition:enter="transition ease-in-out duration-300 transform"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in-out duration-300 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full">
        
        <div class="p-4">
            {{-- Mobile Close Button --}}
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">{{ $subject->name }}</h3>
                <button @click="sidebarOpen = false" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Desktop Header --}}
            <div class="hidden md:block mb-4 pb-4 border-b">
                <div class="flex justify-between items-center">
                    <div class="text-center flex-1">
                        <a href="{{ route('bank-soal.index') }}" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali ke Bank Soal</a>
                        <h3 class="font-bold text-lg text-gray-800 mt-2">{{ $subject->name }}</h3>
                    </div>
                    <button onclick="this.closest('aside').style.display = 'none'; document.getElementById('showSidebarBtn').style.display = 'block';" 
                            class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 ml-2"
                            title="Sembunyikan sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <h4 class="font-semibold text-xs text-gray-500 mb-2 uppercase tracking-wider px-3">Pilih Topik Latihan</h4>
            <nav class="space-y-1">
                @forelse($subject->topics as $topic)
                    {{-- Tombol ini akan memicu event di komponen Livewire PracticeArea --}}
                    <button 
                        onclick="window.selectTopicFromSidebar({{ $topic->id }})"
                        type="button"
                        class="w-full text-left px-3 py-2 rounded-md text-sm transition duration-150 ease-in-out flex justify-between items-center topic-button text-gray-700 hover:bg-gray-100"
                        data-topic-id="{{ $topic->id }}"> 
                        <span>{{ $topic->name }}</span>
                         <svg class="w-4 h-4 text-gray-400 opacity-0 topic-arrow" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    </button>
                @empty
                    <p class="px-3 text-xs text-gray-400">Belum ada topik.</p>
                @endforelse
            </nav>
        </div>
    </aside>

    <main class="w-full md:w-8/12 lg:w-9/12 xl:w-10/12 p-6 md:p-10 order-first md:order-last">
        {{-- Button to show sidebar when hidden (desktop only) --}}
        <button id="showSidebarBtn" 
                onclick="document.querySelector('aside').style.display = 'block'; this.style.display = 'none';"
                class="hidden md:block fixed top-4 left-4 z-10 p-2 bg-white border border-gray-300 rounded-md shadow-md hover:bg-gray-50"
                style="display: none;"
                title="Tampilkan sidebar">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        {{-- Panggil komponen Livewire BARU di sini --}}
        {{-- Beri key unik agar dirender ulang jika subject berubah (jika perlu) --}}
        @livewire('bank-soal.practice-area', ['subject' => $subject], key('practice-area-' . $subject->id)) 
    </main>
</div>

{{-- CSS & JS Tambahan --}}
@push('styles')
<style>
    /* Mobile sidebar styles */
    @media (max-width: 767px) {
        aside {
            transform: translateX(-100%);
        }
        aside.open {
            transform: translateX(0);
        }
    }
    
    /* Topic button styles */
    .topic-button.active { 
        background: linear-gradient(to right, #e0e7ff, #dbeafe);
        color: #1e40af;
        font-weight: 600;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
    .topic-button.active .topic-arrow { 
        opacity: 1; 
        color: #3B82F6; 
    }
    
    /* Prose styles for content */
    .prose p { 
        margin-top: 0; 
        margin-bottom: 0.5em; 
    } 
    button span.prose { 
        display: inline; 
    } 
    .prose-sm { 
        font-size: 0.875rem; 
        line-height: 1.5rem; 
    }
    .prose ul, .prose ol { 
        margin-top: 0.5em; 
        margin-bottom: 0.5em; 
    }
    .prose li p { 
        margin-bottom: 0.2em; 
    }
    
    /* Ensure proper z-index stacking */
    .sidebar-overlay {
        z-index: 20;
    }
    .sidebar-panel {
        z-index: 30;
    }
</style>
@endpush
@push('scripts')
<script>
    // Simple global function for topic selection
    window.selectTopicFromSidebar = function(topicId) {
        console.log('selectTopicFromSidebar called with:', topicId);
        
        // Dispatch a custom event that Livewire can listen to
        window.dispatchEvent(new CustomEvent('topic-selected-from-sidebar', {
            detail: { topicId: topicId }
        }));
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Listener untuk highlight tombol topik aktif
        document.addEventListener('livewire:navigated', () => {
            if (window.Livewire) {
                Livewire.on('topicChanged', (event) => {
                    document.querySelectorAll('.topic-button').forEach(button => {
                        button.classList.remove('active', 'bg-gradient-to-r', 'from-indigo-100', 'to-blue-100', 'text-blue-800', 'font-semibold', 'shadow-inner');
                        button.classList.add('text-gray-700', 'hover:bg-gray-100');
                        const arrow = button.querySelector('.topic-arrow');
                        if (arrow) arrow.style.opacity = '0';
                    });
                    const activeButton = document.querySelector(`.topic-button[data-topic-id="${event.topicId}"]`);
                    if (activeButton) {
                        activeButton.classList.add('active', 'bg-gradient-to-r', 'from-indigo-100', 'to-blue-100', 'text-blue-800', 'font-semibold', 'shadow-inner');
                        activeButton.classList.remove('text-gray-700', 'hover:bg-gray-100');
                         const activeArrow = activeButton.querySelector('.topic-arrow');
                         if(activeArrow) activeArrow.style.opacity = '1';
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection