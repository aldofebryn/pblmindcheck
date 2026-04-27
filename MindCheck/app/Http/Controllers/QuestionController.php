<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\QuestionRequest;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct()
    {
        // Cek session admin di semua method kecuali index & show
        $this->callBeforeCallbacks[] = function ($request, $action) {
            if (!in_array($action, ['index', 'show']) && !session('admin_id')) {
                return redirect()->route('admin.login');
            }
        };
    }

    public function index(Request $request)
    {
        $subskala = $request->get('subskala', 'all');
        $questions = Question::subskala($subskala)
            ->orderBy('nomor')
            ->paginate(15); // ✅ pakai pagination

        if ($request->ajax()) {
            return response()->json([
                'data' => view('admin.questions._table_rows', compact('questions'))->render(),
                'pagination' => $questions->links('pagination::tailwind')->toHtml(),
            ]);
        }

        return view('admin.questions.index', compact('questions', 'subskala'));
    }

    public function create()
    {
        return view('admin.questions.create');
    }

    public function store(QuestionRequest $request)
    {
        Question::create($request->validated());
        return redirect()->route('admin.questions.index')
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function edit(Question $question)
    {
        return view('admin.questions.edit', compact('question'));
    }

    public function update(QuestionRequest $request, Question $question)
    {
        $question->update($request->validated());
        return redirect()->route('admin.questions.index')
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete(); // soft delete
        return redirect()->route('admin.questions.index')
            ->with('success', 'Pertanyaan dipindahkan ke tong sampah.');
    }

    // Opsional: restore & forceDelete
    public function trash()
    {
        $questions = Question::onlyTrashed()->orderBy('nomor')->paginate(15);
        return view('admin.questions.trash', compact('questions'));
    }

    public function restore($id)
    {
        $question = Question::onlyTrashed()->findOrFail($id);
        $question->restore();
        return back()->with('success', 'Pertanyaan berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $question = Question::onlyTrashed()->findOrFail($id);
        $question->forceDelete();
        return back()->with('success', 'Pertanyaan dihapus permanen.');
    }
}