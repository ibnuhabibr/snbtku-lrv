<?php

namespace App\Livewire;

use App\Models\TryoutPackage;
use App\Models\UserTryout;
use App\Models\Subject;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShowTryoutPackageList extends Component
{
    public function viewPackageDetails($packageId)
    {
        $userId = Auth::id();
        
        // 1. Cari UserTryout yang sudah ada (baik ongoing maupun completed)
        $userTryout = UserTryout::where('user_id', $userId)
                                ->where('tryout_package_id', $packageId)
                                ->first();

        // 2. Jika sudah ada UserTryout
        if ($userTryout) {
            if ($userTryout->status === 'completed') {
                // Jika sudah completed, redirect ke halaman hasil keseluruhan
                return $this->redirect(route('tryout.result.overall', $userTryout->id));
            } else {
                // Jika masih ongoing, redirect ke halaman detail untuk melanjutkan
                return $this->redirect(route('tryout.detail', $userTryout->id));
            }
        }

        // 3. Jika belum ada UserTryout, buat baru beserta 7 subtesnya
        $userTryout = UserTryout::create([
            'user_id' => $userId,
            'tryout_package_id' => $packageId,
            'status' => 'ongoing',
            'start_time' => now(), // Tandai kapan paket ini pertama kali dibuka
        ]);

        // Ambil 7 subtes (berdasarkan `subtest_order` yang sudah kita buat)
        $subjects = Subject::whereNotNull('subtest_order')->orderBy('subtest_order')->get();

        foreach ($subjects as $subject) {
            // Buat 7 record progres subtes
            $userTryout->subtestProgresses()->create([
                'subject_id' => $subject->id,
                // Subtes pertama (order = 1) di-unlock, sisanya 'locked'
                'status' => ($subject->subtest_order == 1) ? 'unlocked' : 'locked',
            ]);
        }

        // 4. Redirect ke halaman detail untuk memulai tryout baru
        return $this->redirect(route('tryout.detail', $userTryout->id));
    }

    public function render(): View
    {
        $packages = TryoutPackage::published()
            ->withCount('questions')
            ->get();

        // Ambil data paket tryout beserta status pengerjaan user
        $packagesWithStatus = $packages->map(function ($package) {
            $package->user_status = 'not_started'; // Default status
            $package->user_tryout = null;
            $package->score = null;

            if (Auth::check()) {
                $userTryout = UserTryout::where('user_id', Auth::id())
                    ->where('tryout_package_id', $package->id)
                    ->first();

                if ($userTryout) {
                    $package->user_tryout = $userTryout;
                    $package->user_status = $userTryout->status; // 'ongoing' atau 'completed'
                    
                    if ($userTryout->status === 'completed') {
                        $package->score = $userTryout->score;
                    }
                }
            }

            return $package;
        });

        return view('livewire.show-tryout-package-list', [
            'packages' => $packagesWithStatus,
        ]);
    }
}
