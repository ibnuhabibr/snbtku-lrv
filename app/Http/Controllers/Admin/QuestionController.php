<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Topic;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with(['topic.subject']);

        // Filter by topic if provided
        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        // Filter by subject if provided
        if ($request->filled('subject_id')) {
            $query->whereHas('topic', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        $questions = $query->paginate(10);
        $subjects = Subject::with('topics')->get();
        $topics = Topic::all();

        return view('admin.questions.index', compact('questions', 'subjects', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::with('topics')->get();
        return view('admin.questions.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d,e',
            'explanation' => 'nullable|string',
        ]);

        Question::create($request->all());

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        $question->load(['topic.subject']);
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $subjects = Subject::with('topics')->get();
        return view('admin.questions.edit', compact('question', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d,e',
            'explanation' => 'nullable|string',
        ]);

        $question->update($request->all());

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        // Check if question is used in any tryout packages
        if ($question->tryoutPackages()->count() > 0) {
            return redirect()->route('admin.questions.index')
                ->with('error', 'Tidak dapat menghapus soal yang masih digunakan dalam paket try out.');
        }

        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Get topics by subject (AJAX endpoint)
     */
    public function getTopicsBySubject(Subject $subject)
    {
        return response()->json($subject->topics);
    }
}