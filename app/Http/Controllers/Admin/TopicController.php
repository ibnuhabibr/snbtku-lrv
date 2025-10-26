<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::with('subject')->withCount('questions')->paginate(10);
        return view('admin.topics.index', compact('topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();
        return view('admin.topics.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        Topic::create([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        $topic->load(['subject', 'questions']);
        return view('admin.topics.show', compact('topic'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        $subjects = Subject::all();
        return view('admin.topics.edit', compact('topic', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $topic->update([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        if ($topic->questions()->count() > 0) {
            return redirect()->route('admin.topics.index')
                ->with('error', 'Tidak dapat menghapus topik yang masih memiliki soal.');
        }

        $topic->delete();

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topik berhasil dihapus.');
    }
}