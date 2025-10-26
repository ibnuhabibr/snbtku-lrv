@extends('layouts.app')

@section('title', 'Hasil Try Out: ' . $userTryout->tryoutPackage->title . ' - SNBTKU')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <!-- Livewire Component -->
    <livewire:show-tryout-result :user-tryout-id="$userTryout->id" />
</div>
@endsection