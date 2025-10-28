{{-- resources/views/tryouts/conduct-subtest.blade.php --}}
@extends('layouts.tryout')

@section('content')
    <livewire:conduct-subtest :subtest-progress-id="$subtestProgress->id" />
@endsection