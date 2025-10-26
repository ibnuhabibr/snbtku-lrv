@extends('layouts.app')

@section('title', 'Try Out: ' . $userTryout->tryoutPackage->title . ' - SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Livewire Component -->
    <livewire:conduct-tryout :user-tryout-id="$userTryout->id" />
</div>
@endsection

@push('scripts')
<script>
    // Prevent page refresh/close during tryout
    window.addEventListener('beforeunload', function (e) {
        e.preventDefault();
        e.returnValue = 'Anda sedang mengerjakan try out. Yakin ingin meninggalkan halaman?';
    });

    // Disable right click context menu
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Disable F12, Ctrl+Shift+I, Ctrl+U
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F12' || 
            (e.ctrlKey && e.shiftKey && e.key === 'I') ||
            (e.ctrlKey && e.key === 'u')) {
            e.preventDefault();
        }
    });
</script>
@endpush