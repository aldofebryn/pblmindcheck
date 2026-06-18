<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <h2 class="text-xl font-bold text-slate-800">
                {{ isset($question) ? 'Edit Pertanyaan' : '➕ Tambah Pertanyaan Baru' }}
            </h2>
            <p class="text-slate-500 text-sm mt-0.5">Lengkapi data sesuai instrumen DASS-21</p>
        </div>

        <form action="{{ isset($question) ? route('admin.questions.update', $question->id) : route('admin.questions.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @if(isset($question)) @method('PUT') @endif

            {{-- Nomor Urut --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <label class="sm:w-32 text-sm font-semibold text-slate-700">Nomor Urut <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <input type="number" name="nomor" value="{{ old('nomor', $question->nomor ?? '') }}" 
                           class="w-full sm:w-32 border border-slate-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                           placeholder="1–21">
                    @error('nomor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Subskala --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <label class="sm:w-32 text-sm font-semibold text-slate-700">Subskala <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <select name="subskala" 
                            class="w-full sm:w-48 border border-slate-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @foreach(['Depression' => 'Depresi', 'Anxiety' => 'Kecemasan', 'Stress' => 'Stres'] as $val => $label)
                        <option value="{{ $val }}" {{ (old('subskala', $question->subskala ?? '') == $val) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('subskala') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Pertanyaan Indonesia --}}
            <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                <label class="sm:w-32 text-sm font-semibold text-slate-700 pt-2">Pertanyaan (ID) <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <textarea name="teks_id" rows="3" 
                              class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-y"
                              placeholder="Tulis pertanyaan dalam Bahasa Indonesia...">{{ old('teks_id', $question->teks_id ?? '') }}</textarea>
                    @error('teks_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Pertanyaan Inggris --}}
            <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                <label class="sm:w-32 text-sm font-semibold text-slate-700 pt-2">Pertanyaan (EN) <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <textarea name="teks_en" rows="3" 
                              class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 italic resize-y"
                              placeholder="Write the question in English...">{{ old('teks_en', $question->teks_en ?? '') }}</textarea>
                    @error('teks_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.questions.index') }}" 
                   class="px-5 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition text-sm">Batal</a>
                <button type="submit" 
                        class="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm transition text-sm">
                    Simpan Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>