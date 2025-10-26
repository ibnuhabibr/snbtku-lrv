<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\TryoutPackage;
use App\Models\UserTryout;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_subjects' => Subject::count(),
            'total_topics' => Topic::count(),
            'total_questions' => Question::count(),
            'total_tryout_packages' => TryoutPackage::count(),
            'total_tryouts_taken' => UserTryout::count(),
            'total_posts' => Post::count(),
        ];

        $recent_tryouts = UserTryout::with(['user', 'tryoutPackage'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_tryouts'));
    }
}
