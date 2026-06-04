<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\GuardsAdmin;
use App\Models\Patient;
use App\Models\AdminLog;
use App\Models\Screening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    use GuardsAdmin;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->guardAdmin();

        $sortable = ['id', 'username', 'umur', 'created_at', 'screenings_count'];
        $sort  = in_array($request->get('sort', 'created_at'), $sortable)
                    ? $request->get('sort', 'created_at')
                    : 'created_at';
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Patient::withCount(['screenings' => fn($q) => $q->whereNotNull('selesai_at')]);

        if ($sort === 'screenings_count') {
            $query->orderBy('screenings_count', $order);
        } else {
            $query->orderBy($sort, $order);
        }

        $patients = $query->get();

        return view('admin.patients.index', compact('patients', 'sort', 'order'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->guardAdmin();
        return view('admin.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->guardAdmin();

        $request->validate([
            'username' => 'nullable|string|unique:patients,username|max:255',
            'password' => 'nullable|string|min:6',
            'umur' => 'nullable|integer|min:1',
            'status_pekerjaan' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        Patient::create([
            'username' => $request->username,
            'password' => $request->password ? Hash::make($request->password) : null,
            'umur' => $request->umur,
            'status_pekerjaan' => $request->status_pekerjaan,
            'alias' => $request->alias,
            'admin_notes' => $request->admin_notes,
        ]);

        AdminLog::record(
            'Menambah data pasien',
            'Pasien',
            'Admin menambahkan pasien baru: ' . $request->username
        );

        return redirect()->route('admin.patients.index')
            ->with('success', 'Pasien berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $patient_id)
    {
        $this->guardAdmin();

        $patient = Patient::findOrFail($patient_id);

        $screenings = Screening::where('patient_id', $patient_id)
            ->with('result')
            ->whereNotNull('selesai_at')
            ->orderBy('created_at')
            ->get();

        $chartData = $screenings->take(10)->reverse()->values()->map(fn($s) => [
            'tanggal'   => $s->selesai_at->format('d M'),
            'depresi'   => $s->result?->skor_depresi   ?? 0,
            'kecemasan' => $s->result?->skor_kecemasan ?? 0,
            'stres'     => $s->result?->skor_stres     ?? 0,
        ]);

        return view('admin.patients.show', compact('patient', 'screenings', 'chartData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $patient_id)
    {
        $this->guardAdmin();

        $patient = Patient::findOrFail($patient_id);
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $patient_id)
    {
        $this->guardAdmin();

        $patient = Patient::findOrFail($patient_id);

        $request->validate([
            'username' => 'nullable|string|max:255|unique:patients,username,' . $patient_id . ',id',
            'password' => 'nullable|string|min:6',
            'umur' => 'nullable|integer|min:1',
            'status_pekerjaan' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        $oldUsername = $patient->username;
        $oldUmur = $patient->umur;
        $oldStatus = $patient->status_pekerjaan;

        $data = [
            'username' => $request->username,
            'umur' => $request->umur,
            'status_pekerjaan' => $request->status_pekerjaan,
            'alias' => $request->alias,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $patient->update($data);

        AdminLog::record(
            'Mengubah data pasien',
            'Pasien',
            'Admin mengubah pasien ' . $oldUsername .
            ' | Umur: ' . $oldUmur . ' → ' . $patient->umur .
            ' | Status: ' . $oldStatus . ' → ' . $patient->status_pekerjaan
        );

        return redirect()->route('admin.patients.index')
            ->with('success', 'Data Pasien berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $patient_id)
    {
        $this->guardAdmin();

        $patient = Patient::findOrFail($patient_id);
        $patientName = $patient->username;

        $patient->delete();

        AdminLog::record(
            'Menghapus data pasien',
            'Pasien',
            'Admin memindahkan data pasien ke tempat sampah: ' . $patientName
        );

        return redirect()->route('admin.patients.index')
            ->with('success', 'Pasien berhasil dipindahkan ke tempat sampah');
    }

    public function trash()
    {
        $this->guardAdmin();
        $patients = Patient::onlyTrashed()
            ->withCount(['screenings' => fn($q) => $q->whereNotNull('selesai_at')])
            ->orderByDesc('deleted_at')
            ->get();
        return view('admin.patients.trash', compact('patients'));
    }

    public function restore($id)
    {
        $this->guardAdmin();
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        AdminLog::record(
            'Memulihkan data pasien',
            'Pasien',
            'Admin memulihkan data pasien: ' . $patient->username
        );

        return back()->with('success', 'Pasien berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $this->guardAdmin();
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patientName = $patient->username;
        $patient->forceDelete();

        AdminLog::record(
            'Hapus permanen pasien',
            'Pasien',
            'Admin menghapus secara permanen data pasien: ' . $patientName
        );

        return back()->with('success', 'Pasien beserta seluruh riwayatnya berhasil dihapus secara permanen.');
    }
}
