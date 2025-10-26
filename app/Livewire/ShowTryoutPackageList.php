<?php

namespace App\Livewire;

use App\Models\TryoutPackage;
use App\Models\UserTryout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ShowTryoutPackageList extends Component
{
    public function startTryout($packageId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user already has an ongoing tryout for this package
        $existingTryout = UserTryout::where('user_id', Auth::id())
            ->where('tryout_package_id', $packageId)
            ->where('status', 'ongoing')
            ->first();

        if ($existingTryout) {
            // Continue existing tryout
            return redirect()->route('tryout.conduct', $existingTryout->id);
        }

        // Check if user has already completed this package
        $completedTryout = UserTryout::where('user_id', Auth::id())
            ->where('tryout_package_id', $packageId)
            ->where('status', 'completed')
            ->first();

        if ($completedTryout) {
            // Show result instead of starting new tryout
            return redirect()->route('tryout.result', $completedTryout->id);
        }

        // Create new tryout session
        $userTryout = UserTryout::create([
            'user_id' => Auth::id(),
            'tryout_package_id' => $packageId,
            'start_time' => now(),
            'status' => 'ongoing',
        ]);

        return redirect()->route('tryout.conduct', $userTryout->id);
    }

    public function render()
    {
        $packages = TryoutPackage::published()
            ->with('questions')
            ->get();

        // Get user's tryout status for each package
        $userTryouts = [];
        if (Auth::check()) {
            $userTryouts = UserTryout::where('user_id', Auth::id())
                ->whereIn('tryout_package_id', $packages->pluck('id'))
                ->get()
                ->keyBy('tryout_package_id');
        }

        return view('livewire.show-tryout-package-list', [
            'packages' => $packages,
            'userTryouts' => $userTryouts,
        ]);
    }
}
