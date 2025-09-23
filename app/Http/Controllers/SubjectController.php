<?php

namespace App\Http\Controllers;

use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::select('id','name','slug','description')->orderBy('name')->get();
    }

    public function show(string $slug)
    {
        $subject = Subject::where('slug', $slug)
            ->with(['books' => function($q){ $q->select('id','subject_id','title','author','description','file_path','created_at'); }])
            ->firstOrFail();
        return response()->json($subject);
    }
}
