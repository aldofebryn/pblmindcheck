<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\GuardsAdmin;
use App\Models\Question;
use App\Http\Requests\QuestionRequest;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use GuardsAdmin;

    // ── Daftar pertanyaan ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->guardAdmin();

        $subskala  = $request->get('subskala', 'all');
        $questions = Question::subskala($subskala)
            ->orderBy('nomor')
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'data'       => view('admin.questions._table_rows', compact('questions'))->render(),
                'pagination' => $questions->links('pagination::tailwind')->toHtml(),
            ]);
        }

        return view('admin.questions.index', compact('questions', 'subskala'));
    }

    // ── Form tambah ───────────────────────────────────────────────
    public function create()
    {
        $this->guardAdmin();
        return view('admin.questions.create');
    }

    // ── Simpan ────────────────────────────────────────────────────
    public function store(QuestionRequest $request)
    {
        $this->guardAdmin();
        Question::create($request->validated());
        return redirect()->route('admin.questions.index')
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    // ── Form edit ─────────────────────────────────────────────────
    public function edit(Question $question)
    {
        $this->guardAdmin();
        return view('admin.questions.edit', compact('question'));
    }

    // ── Update ────────────────────────────────────────────────────
    public function update(QuestionRequest $request, Question $question)
    {
        $this->guardAdmin();
        $question->update($request->validated());
        return redirect()->route('admin.questions.index')
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    // ── Soft delete ───────────────────────────────────────────────
    public function destroy(Question $question)
    {
        $this->guardAdmin();
        $question->delete();
        return redirect()->route('admin.questions.index')
            ->with('success', 'Pertanyaan dipindahkan ke tong sampah.');
    }

    // ── Tong sampah ───────────────────────────────────────────────
    public function trash()
    {
        $this->guardAdmin();
        $questions = Question::onlyTrashed()->orderBy('nomor')->paginate(15);
        return view('admin.questions.trash', compact('questions'));
    }

    // ── Pulihkan ──────────────────────────────────────────────────
    public function restore($id)
    {
        $this->guardAdmin();
        Question::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Pertanyaan berhasil dipulihkan.');
    }

    // ── Hapus permanen ────────────────────────────────────────────
    public function forceDelete($id)
    {
        $this->guardAdmin();
        Question::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Pertanyaan dihapus permanen.');
    }
}
