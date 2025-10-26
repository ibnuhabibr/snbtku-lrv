<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TryoutPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = TryoutPackage::withCount('questions')->paginate(10);
        return view('admin.tryout-packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tryout-packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        TryoutPackage::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'duration_minutes' => $request->duration_minutes,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tryout-packages.index')
            ->with('success', 'Paket try out berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TryoutPackage $tryoutPackage)
    {
        $tryoutPackage->load(['questions.topic.subject']);
        return view('admin.tryout-packages.show', compact('tryoutPackage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TryoutPackage $tryoutPackage)
    {
        return view('admin.tryout-packages.edit', compact('tryoutPackage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TryoutPackage $tryoutPackage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        $tryoutPackage->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'duration_minutes' => $request->duration_minutes,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tryout-packages.index')
            ->with('success', 'Paket try out berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TryoutPackage $tryoutPackage)
    {
        // Check if package has been used by users
        if ($tryoutPackage->userTryouts()->count() > 0) {
            return redirect()->route('admin.tryout-packages.index')
                ->with('error', 'Tidak dapat menghapus paket try out yang sudah pernah dikerjakan oleh pengguna.');
        }

        $tryoutPackage->delete();

        return redirect()->route('admin.tryout-packages.index')
            ->with('success', 'Paket try out berhasil dihapus.');
    }

    /**
     * Show questions management for the package
     */
    public function questions(TryoutPackage $tryoutPackage)
    {
        $packageQuestions = $tryoutPackage->questions()->with(['topic.subject'])->get();
        $subjects = Subject::with(['topics.questions'])->get();
        
        return view('admin.tryout-packages.questions', compact('tryoutPackage', 'packageQuestions', 'subjects'));
    }

    /**
     * Add question to package
     */
    public function addQuestion(Request $request, TryoutPackage $tryoutPackage)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
        ]);

        // Check if question is already in the package
        if ($tryoutPackage->questions()->where('question_id', $request->question_id)->exists()) {
            return back()->with('error', 'Soal sudah ada dalam paket ini.');
        }

        // Get the next order number
        $nextOrder = $tryoutPackage->questions()->max('order') + 1;

        $tryoutPackage->questions()->attach($request->question_id, ['order' => $nextOrder]);

        return back()->with('success', 'Soal berhasil ditambahkan ke paket.');
    }

    /**
     * Remove question from package
     */
    public function removeQuestion(TryoutPackage $tryoutPackage, Question $question)
    {
        $tryoutPackage->questions()->detach($question->id);

        // Reorder remaining questions
        $remainingQuestions = $tryoutPackage->questions()->orderBy('order')->get();
        foreach ($remainingQuestions as $index => $q) {
            $tryoutPackage->questions()->updateExistingPivot($q->id, ['order' => $index + 1]);
        }

        return back()->with('success', 'Soal berhasil dihapus dari paket.');
    }

    /**
     * Update question order in package
     */
    public function updateQuestionOrder(Request $request, TryoutPackage $tryoutPackage)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:questions,id',
        ]);

        foreach ($request->questions as $index => $questionId) {
            $tryoutPackage->questions()->updateExistingPivot($questionId, ['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}