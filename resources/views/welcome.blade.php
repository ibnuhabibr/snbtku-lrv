@extends('layouts.app')

@section('title', 'SNBTKU - Platform Persiapan SNBT Gratis')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#grid)" />
        </svg>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="text-center">
            <div class="mb-8">
                <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium mb-6">
                    🎯 Platform Persiapan SNBT #1 di Indonesia
                </span>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-tight">
                Raih Impian
                <span class="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                    PTN Favoritmu
                </span>
            </h1>
            <p class="text-xl md:text-2xl mb-12 text-blue-100 max-w-4xl mx-auto leading-relaxed">
                Platform e-learning gratis dengan ribuan soal latihan, try out berkualitas, dan pembahasan lengkap untuk persiapan SNBT terbaik
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('tryouts.index') }}" class="group bg-white text-blue-600 px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                    <span class="flex items-center">
                        <svg class="w-6 h-6 mr-3 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Mulai Try Out Gratis
                    </span>
                </a>
                @guest
                <a href="{{ route('register') }}" class="group border-2 border-white text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-blue-600 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                    <span class="flex items-center">
                        <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Daftar Gratis Sekarang
                    </span>
                </a>
                @endguest
            </div>
        </div>
    </div>
    
    <!-- Floating Elements -->
    <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-16 h-16 bg-yellow-400/20 rounded-full animate-bounce"></div>
    <div class="absolute top-1/2 right-20 w-12 h-12 bg-purple-400/20 rounded-full animate-ping"></div>
</section>

<!-- Features Section -->
<section class="py-24 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <span class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                ✨ Fitur Unggulan
            </span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Mengapa Memilih 
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SNBTKU</span>?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Platform terlengkap untuk persiapan SNBT dengan fitur-fitur unggulan yang akan membantumu meraih skor terbaik
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group bg-white p-10 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Bank Soal Lengkap</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Ribuan soal SNBT terbaru dengan pembahasan detail untuk semua materi TPS, Literasi, dan Numerasi.
                </p>
                <div class="flex justify-center">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">5000+ Soal</span>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="group bg-white p-10 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Try Out Realistis</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Simulasi ujian SNBT dengan timer, sistem penilaian otomatis, dan analisis hasil yang mendalam.
                </p>
                <div class="flex justify-center">
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Real-time</span>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="group bg-white p-10 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Analisis Performa</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Tracking progress belajar, identifikasi kelemahan, dan rekomendasi materi yang perlu dipelajari.
                </p>
                <div class="flex justify-center">
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">AI Powered</span>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="group bg-white p-10 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Materi Pembelajaran</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Artikel, tips & trik, dan strategi mengerjakan soal SNBT dari para ahli dan alumni PTN.
                </p>
                <div class="flex justify-center">
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">Expert Tips</span>
                </div>
            </div>

            <!-- Feature 5 -->
            <div class="group bg-white p-10 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">100% Gratis</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Semua fitur dapat diakses gratis tanpa biaya tersembunyi. Komitmen kami untuk pendidikan Indonesia.
                </p>
                <div class="flex justify-center">
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">No Hidden Fee</span>
                </div>
            </div>

            <!-- Feature 6 -->
            <div class="group bg-white p-10 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Update Berkala</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    Soal dan materi selalu diperbarui mengikuti perkembangan terbaru format SNBT.
                </p>
                <div class="flex justify-center">
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">Always Fresh</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px), radial-gradient(circle at 75% 75%, white 2px, transparent 2px); background-size: 50px 50px;"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Dipercaya Ribuan Siswa
            </h2>
            <p class="text-xl text-blue-100">
                Bergabunglah dengan komunitas belajar terbesar untuk persiapan SNBT
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            <div class="group">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">10K+</div>
                    <div class="text-blue-100 font-medium">Siswa Terdaftar</div>
                </div>
            </div>
            <div class="group">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text text-transparent">50K+</div>
                    <div class="text-blue-100 font-medium">Try Out Diselesaikan</div>
                </div>
            </div>
            <div class="group">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-pink-400 to-red-500 bg-clip-text text-transparent">5K+</div>
                    <div class="text-blue-100 font-medium">Bank Soal</div>
                </div>
            </div>
            <div class="group">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-purple-400 to-indigo-500 bg-clip-text text-transparent">95%</div>
                    <div class="text-blue-100 font-medium">Tingkat Kepuasan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full">
        <div class="absolute top-20 left-20 w-32 h-32 bg-blue-500/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-purple-500/10 rounded-full blur-xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-indigo-500/5 rounded-full blur-2xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-8">
            <span class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-full text-sm font-semibold mb-6">
                🚀 Mulai Sekarang
            </span>
        </div>
        <h2 class="text-4xl md:text-6xl font-bold mb-8 leading-tight">
            Siap Menaklukkan 
            <span class="bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">SNBT</span>?
        </h2>
        <p class="text-xl md:text-2xl text-gray-300 mb-12 leading-relaxed max-w-3xl mx-auto">
            Mulai persiapanmu sekarang dan raih skor terbaik di SNBT 2024! Bergabunglah dengan ribuan siswa lainnya.
        </p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <a href="{{ route('tryouts.index') }}" 
               class="group bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-blue-500/25">
                <span class="flex items-center">
                    <svg class="w-6 h-6 mr-3 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Mulai Try Out Sekarang
                </span>
            </a>
            @guest
            <a href="{{ route('register') }}" 
               class="group border-2 border-white text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-gray-900 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-white/25">
                <span class="flex items-center">
                    <svg class="w-6 h-6 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Daftar Akun Gratis
                </span>
            </a>
            @endguest
        </div>
    </div>
</section>
@endsection
