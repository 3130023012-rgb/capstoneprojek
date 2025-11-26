<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trainer;

class TrainerController extends Controller
{
    public function create()
    {
        return view('trainer_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:trainers,name',
        ]);

        Trainer::create($validated);

        return redirect()->route('absensi.create')->with('success', 'Pelatih baru berhasil ditambahkan!');
    }
}