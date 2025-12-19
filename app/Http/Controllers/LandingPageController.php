<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Gallery;
use App\Models\Setting;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil data jadwal (termasuk proof_photo)
        $schedules = Schedule::all(); 

        // Ambil data foto galeri umum yang aktif
        $photos = Gallery::where('is_active', 1)
                           ->orderBy('created_at', 'desc')
                           ->limit(6) 
                           ->get();
                           $settings = Setting::pluck('value', 'key')->toArray();

        // Kirim semua data ke view landing page
        return view('landing_page', compact('schedules', 'photos','settings'));
    }
}