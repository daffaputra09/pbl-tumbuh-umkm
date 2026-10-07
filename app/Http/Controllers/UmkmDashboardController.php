<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UmkmDashboardController extends Controller
{
     public function index(Request $request): View|RedirectResponse
    {
        $business = Business::where('user_id', $request->user()->id)->first();
 
        if ($business === null) {
            return redirect()
                ->route('umkm.profil')
                ->with('status', 'Lengkapi profil usaha dulu ya, baru dashboard-nya bisa ditampilkan.');
        }
 
        return view('umkm.dashboard', [
            'business' => [
                'name' => $business->business_name,
                'verificationStatus' => $business->verification_status,
                'verificationNote' => $business->verification_note,
            ],
            'ownerName' => $request->user()->name,
        ]);
    }
}
