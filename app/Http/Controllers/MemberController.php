<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function create()
    {
        return view('member_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'member_id' => 'nullable|string|max:50|unique:members,member_id',
        ]);

        Member::create($validated);

        return redirect()->route('absensi.create')->with('success', 'Anggota baru berhasil ditambahkan!');
    }
}