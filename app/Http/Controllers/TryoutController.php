<?php

namespace App\Http\Controllers;

use App\Models\TryoutPackage;
use App\Models\UserTryout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TryoutController extends Controller
{
    /**
     * Display a listing of tryout packages.
     */
    public function index()
    {
        return view('tryouts.index');
    }

    /**
     * Show the conduct tryout page.
     */
    public function conduct(UserTryout $userTryout)
    {
        // Check if user owns this tryout
        if ($userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this tryout.');
        }

        // Check if tryout is still ongoing
        if ($userTryout->status !== 'ongoing') {
            return redirect()->route('tryout.result', $userTryout->id);
        }

        return view('tryouts.conduct', compact('userTryout'));
    }

    /**
     * Show the tryout result page.
     */
    public function result(UserTryout $userTryout)
    {
        // Check if user owns this tryout
        if ($userTryout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this tryout result.');
        }

        // Check if tryout is completed
        if ($userTryout->status !== 'completed') {
            return redirect()->route('tryout.conduct', $userTryout->id);
        }

        return view('tryouts.result', compact('userTryout'));
    }
}
