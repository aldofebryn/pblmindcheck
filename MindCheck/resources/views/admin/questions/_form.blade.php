<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <h2 class="text-xl font-bold text-slate-800">
                {{ isset($question) ? '✏️ Edit Pertanyaan' : '➕ Tambah Pertanyaan Baru' }}
            </h2>
            <p class="text-slate-500 text-sm mt-1">Lengkapi data sesuai instrumen DASS-21</p>
        </div>

        <form action="{{ isset($question) ? route('admin.questions.update', $question->id) : route('admin.questions.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @if(isset($question)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nomor Urut --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Urut <span class="text-red-500">*</span></label>
                    <input type="number" name="nomor" value="{{ old('nomor', $question->nomor ?? '') }}" 
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 transition" placeholder="1–21">
                    @error('nomor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Subskala --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Subskala <span class="text-red-500">*</span></label>
                    <select name="subskala" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @foreach(['Depression' => 'Depresi', 'Anxiety' => 'Kecemasan', 'Stress' => 'Stres'] as $val => $label)
                        <option value="{{ $val }}" {{ (old('subskala', $question->subskala ?? '') == $val) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('subskala') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Teks Indonesia --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pertanyaan (Bahasa Indonesia) <span class="text-red-500">*</span></label>
                <textarea name="teks_id" rows="3" 
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 resize-y">{{ old('teks_id', $question->teks_id ?? '') }}</textarea>
                @error('teks_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Teks Inggris --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pertanyaan (English) <span class="text-red-500">*</span></label>
                <textarea name="teks_en" rows="3" 
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 italic resize-y">{{ old('teks_en', $question->teks_en ?? '') }}</textarea>
                @error('teks_en') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.questions.index') }}" 
                   class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Batal</a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-sm transition">
                    Simpan Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>