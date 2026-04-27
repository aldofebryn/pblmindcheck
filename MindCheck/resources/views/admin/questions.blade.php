@extends('layouts.admin')
@section('title','Manajemen Pertanyaan DASS-21')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">{{ isset($question) ? 'Perbarui' : 'Tambah' }} Pertanyaan DASS-21</h2>
        
        <form action="{{ isset($question) ? route('questions.update', $question->id) : route('questions.store') }}" method="POST">
            @csrf
            @if(isset($question)) @method('PUT') @endif

            <div class="grid gap-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Urut</label>
                        <input type="number" name="nomor" value="{{ old('nomor', $question->nomor ?? '') }}" 
                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Subskala</label>
                        <select name="subskala" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['Depression','Anxiety','Stress'] as $cat)
                                <option value="{{ $cat }}" {{ (old('subskala', $question->subskala ?? '') == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pertanyaan (Bahasa Indonesia)</label>
                    <textarea name="teks_id" rows="3" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('teks_id', $question->teks_id ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pertanyaan (English)</label>
                    <textarea name="teks_en" rows="3" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 italic">{{ old('teks_en', $question->teks_en ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition-all">
                    Simpan Perubahan
                </button>
                <a href="{{ route('questions.index') }}" class="bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<div class="mb-7 flex flex-wrap items-center justify-between gap-5">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">DASS-21 Questions</h1>
        <p class="text-slate-400 max-w-xl leading-relaxed mt-1">
            Manajemen instrumen DASS-21 untuk aplikasi Mind Check.
        </p>
    </div>
    <a href="{{ route('questions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm shadow-blue-200">
        + Tambah Pertanyaan
    </a>
</div>

<div class="flex gap-2 flex-wrap mb-6">
    @foreach(['all'=>'Semua','Depression'=>'Depresi','Anxiety'=>'Kecemasan','Stress'=>'Stres'] as $val=>$label)
    <button onclick="filterCat('{{ $val }}')" data-cat="{{ $val }}"
            class="cat-btn font-semibold px-4 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-blue-50 transition-colors {{ $val==='all' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
        {{ $label }}
    </button>
    @endforeach
</div>

<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div id="question-list" class="divide-y divide-slate-50">
        @foreach($questions as $q)
        @php
            $c = match($q->subskala){ 'Depression'=>'blue','Anxiety'=>'violet','Stress'=>'orange',default=>'slate'};
        @endphp
        <div class="q-row px-7 py-5 hover:bg-slate-50/70 transition-colors flex items-center gap-5" data-cat="{{ $q->subskala }}">
            <span class="w-10 h-10 bg-{{ $c }}-50 text-{{ $c }}-600 rounded-xl flex items-center justify-center font-bold shrink-0">
                {{ str_pad($q->nomor, 2, '0', STR_PAD_LEFT) }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 leading-snug">{{ $q->teks_id }}</p>
                <p class="text-slate-400 italic text-sm">{{ $q->teks_en }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                 <span class="shrink-0 text-xs font-bold px-3 py-1 rounded-full border bg-{{ $c }}-50 text-{{ $c }}-700 border-{{ $c }}-200 uppercase">
                    {{ $q->subskala }}
                </span>
                <div class="flex gap-1">
                    <a href="{{ route('questions.edit', $q->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?')">
                        @csrf @method('DELETE')
                        <button class="p-2 text-slate-400 hover:text-red-600 transition-colors">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="mb-7 flex flex-wrap items-center justify-between gap-5">
    <p class="text-slate-400 max-w-xl leading-relaxed">
        Instrumen DASS-21 tervalidasi ilmiah (Lovibond &amp; Lovibond, 1995).
        Pertanyaan bersifat <strong class="text-slate-600">read-only</strong> agar validitas psikometrik terjaga.
    </p>
    <div class="flex gap-2 flex-wrap">
        @foreach(['all'=>'Semua','Depression'=>'Depresi','Anxiety'=>'Kecemasan','Stress'=>'Stres'] as $val=>$label)
        <button onclick="filterCat('{{ $val }}')" data-cat="{{ $val }}"
                class="cat-btn font-semibold px-4 py-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors
                       {{ $val==='all' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
</div>

<div class="flex gap-3 mb-6 flex-wrap">
    @foreach([['Depression','Depresi','blue'],['Anxiety','Kecemasan','violet'],['Stress','Stres','orange']] as [$key,$lbl,$c])
    <span class="inline-flex items-center gap-2 font-semibold text-{{ $c }}-700 bg-{{ $c }}-50 border border-{{ $c }}-200 px-4 py-2 rounded-full">
        <span class="w-2 h-2 bg-{{ $c }}-500 rounded-full"></span>
        {{ $lbl }} ({{ $questions->where('subskala',$key)->count() }} soal)
    </span>
    @endforeach
</div>

<div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    <div id="question-list" class="divide-y divide-slate-50">
        @foreach($questions as $q)
        @php
        $c = match($q->subskala){ 'Depression'=>'blue','Anxiety'=>'violet','Stress'=>'orange',default=>'slate'};
        $lid = match($q->subskala){'Depression'=>'Depresi','Anxiety'=>'Kecemasan','Stress'=>'Stres',default=>$q->subskala};
        @endphp
        <div class="q-row px-7 py-5 hover:bg-slate-50/70 transition-colors flex items-start gap-5" data-cat="{{ $q->subskala }}">
            <span class="w-10 h-10 bg-{{ $c }}-50 text-{{ $c }}-600 rounded-xl flex items-center justify-center font-bold shrink-0 mt-0.5">
                {{ str_pad($q->nomor,2,'0',STR_PAD_LEFT) }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 leading-snug text-lg">{{ $q->teks_id }}</p>
                <p class="text-slate-400 italic mt-1">{{ $q->teks_en }}</p>
            </div>
            <span class="shrink-0 font-semibold px-3 py-1.5 rounded-full border bg-{{ $c }}-50 text-{{ $c }}-700 border-{{ $c }}-200">
                {{ $lid }}
            </span>
        </div>
        @endforeach
    </div>
    <div class="px-7 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
        <p class="text-slate-400">Total: <strong id="visible-count" class="text-slate-600">{{ $questions->count() }}</strong> pertanyaan</p>
        <p class="text-slate-400 text-sm">Sumber: Lovibond &amp; Lovibond (1995). UNSW, Australia.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterCat(cat){
    let count=0;
    document.querySelectorAll('.q-row').forEach(r=>{
        const show=cat==='all'||r.dataset.cat===cat;
        r.style.display=show?'':'none';
        if(show)count++;
    });
    document.getElementById('visible-count').textContent=count;
    document.querySelectorAll('.cat-btn').forEach(b=>{
        const a=b.dataset.cat===cat;
        b.classList.toggle('bg-blue-50',a);b.classList.toggle('text-blue-700',a);b.classList.toggle('border-blue-200',a);
        b.classList.toggle('text-slate-500',!a);b.classList.toggle('border-slate-200',!a);
    });
}
</script>
@endpush