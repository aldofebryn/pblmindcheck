<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Skrining DASS-21 — MindCheck</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 antialiased min-h-screen flex flex-col">

<header class="bg-white rounded-b-2xl shadow-sm border-b border-slate-100 sticky top-0 z-40">
    <div class="w-full px-4 lg:px-6 py-2.5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo1.png') }}" alt="MindCheck" class="h-10 w-auto">
            <span class="font-black text-slate-900 text-lg">Skrining DASS-21</span>
        </div>
        <a href="{{ route('patient.dashboard') }}"
           onclick="confirmExit(event, this.href)"
           class="flex items-center gap-2 px-4 py-2 text-slate-500 hover:text-blue-600 text-sm font-bold rounded-lg hover:bg-slate-100 transition">
            Keluar
        </a>
    </div>
</header>

<main class="flex-1" id="app">
    <div class="max-w-[960px] mx-auto px-4 lg:px-6 py-4">
        <form method="POST" action="{{ route('screening.submit') }}" id="screening-form">
            @csrf
            <div id="hidden-answers"></div>

            <div class="pt-2 pb-4">
                <div class="flex items-end justify-between mb-3">
                    <div>
                        <span id="current-num" class="font-black text-blue-600 text-4xl">1</span>
                        <span class="text-slate-400 font-bold ml-1.5 text-lg">/ {{ $questions->count() }}</span>
                    </div>
                    <div class="text-right">
                        <span id="category-label" class="text-base font-black text-blue-600 uppercase tracking-wider"></span>
                        <span id="pct-label" class="block text-xs text-slate-500 mt-0.5"></span>
                    </div>
                </div>
                <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div id="progress-bar" class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>

            <div id="question-card" class="bg-white rounded-2xl shadow-md shadow-slate-200/50 border border-slate-100 p-5 mb-4 flex items-center justify-between gap-4">
                <div>
                    <p id="question-en" class="text-slate-400 italic font-semibold text-xs mb-2"></p>
                    <h2 id="question-text" class="text-xl md:text-2xl font-normal text-slate-950 leading-snug font-serif"></h2>
                </div>
                <div class="hidden lg:flex w-40 justify-center">
                    <svg viewBox="0 0 360 160" class="w-64 h-28">
                        <circle cx="300" cy="34" r="18" fill="#FBBF24"/>
                        <ellipse cx="165" cy="120" rx="125" ry="8" fill="#BFDBFE"/>
                        <path d="M55 95c18-26 46-34 72-25 14-28 57-31 78-5 30-6 62 13 65 43H55z" fill="#DBEAFE"/>
                        <path d="M95 102c12-18 33-24 52-18 10-21 43-23 58-4 22-4 45 9 47 30H95z" fill="#BFDBFE"/>
                        <circle cx="166" cy="82" r="5" fill="#3B82F6" opacity=".55"/>
                        <circle cx="192" cy="82" r="5" fill="#3B82F6" opacity=".55"/>
                        <path d="M164 100c8 9 23 9 31 0" stroke="#3B82F6" stroke-width="5" stroke-linecap="round" fill="none" opacity=".55"/>
                        <path d="M45 122c8-20 17-29 29-35" stroke="#60A5FA" stroke-width="6" stroke-linecap="round" fill="none"/>
                        <path d="M61 111c-15-4-22-12-26-25" stroke="#60A5FA" stroke-width="6" stroke-linecap="round" fill="none"/>
                        <path d="M278 122c-8-20-17-29-29-35" stroke="#60A5FA" stroke-width="6" stroke-linecap="round" fill="none"/>
                        <path d="M262 111c15-4 22-12 26-25" stroke="#60A5FA" stroke-width="6" stroke-linecap="round" fill="none"/>
                        <circle cx="80" cy="45" r="5" fill="#DBEAFE"/>
                        <circle cx="245" cy="55" r="5" fill="#DBEAFE"/>
                        <rect x="230" y="62" width="65" height="8" rx="4" fill="#DBEAFE"/>
                        <rect x="42" y="55" width="55" height="8" rx="4" fill="#DBEAFE"/>
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6" id="options">
                @php
                $opsi = [
                    [0,'Tidak pernah',  'Tidak berlaku sama sekali untuk saya','green'],
                    [1,'Jarang', 'Berlaku untuk saya sesekali','yellow'],
                    [2,'Sering',        'Berlaku dalam kadar yang cukup besar','orange'],
                    [3,'Sangat sering', 'Sangat berlaku, hampir sepanjang waktu','rose'],
                ];
                @endphp

                @foreach($opsi as [$val,$label,$desc,$icon])
                <label class="option-btn group flex items-center justify-between bg-white border border-slate-100 rounded-2xl px-5 py-4 cursor-pointer hover:border-blue-300 hover:shadow-md transition-all"
                       data-value="{{ $val }}"
                       onclick="selectAnswer({{ $val }})">
                    <div class="flex items-center gap-4">
                        <div class="check-circle w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center flex-shrink-0">
                            <div class="check-dot hidden w-2.5 h-2.5 rounded-full bg-blue-600"></div>
                        </div>
                        <div>
                            <div class="font-black text-base text-slate-900 group-hover:text-blue-700">{{ $label }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $desc }}</div>
                        </div>
                    </div>
                    <div class="hidden md:flex w-10 h-10 rounded-xl items-center justify-center flex-shrink-0
                        {{ $icon === 'green'  ? 'bg-emerald-100 text-emerald-500' : '' }}
                        {{ $icon === 'yellow' ? 'bg-amber-100 text-amber-500'    : '' }}
                        {{ $icon === 'orange' ? 'bg-orange-100 text-orange-500'  : '' }}
                        {{ $icon === 'rose'   ? 'bg-rose-100 text-rose-500'      : '' }}">
                        @if($icon === 'green')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" d="M8.5 10h.01M15.5 10h.01"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 14c1.8 2 5.2 2 7 0"/>
                            </svg>
                        @elseif($icon === 'yellow')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="5" y="6" width="14" height="13" rx="2"/>
                                <path stroke-linecap="round" d="M8 4v4M16 4v4M5 10h14"/>
                                <path stroke-linecap="round" d="M9 14h.01M12 14h.01M15 14h.01"/>
                            </svg>
                        @elseif($icon === 'orange')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14"/>
                                <rect x="6" y="13" width="3" height="6" rx="1"/>
                                <rect x="11" y="10" width="3" height="9" rx="1"/>
                                <rect x="16" y="7" width="3" height="12" rx="1"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l4-3 4 2 4-5"/>
                            </svg>
                        @elseif($icon === 'rose')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                            </svg>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="prevQuestion()" id="btn-prev"
                       class="flex items-center px-5 py-2.5 rounded-xl border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white disabled:opacity-0 transition text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </button>
                <div class="flex items-center gap-2 text-slate-500 font-medium text-sm">
                    <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-3.5 7-10V5l-7-3-7 3v6c0 6.5 7 10 7 10z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-5"/>
                        </svg>
                    </div>
                    <span>Progress tersimpan otomatis</span>
                </div>
                <button type="button" onclick="nextQuestion()" id="btn-next"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 shadow-md shadow-blue-200 transition">
                    Next
                </button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const QUESTIONS    = @json($questionsFormatted);
const LABEL_MAP    = {Depression:'Depresi', Anxiety:'Kecemasan', Stress:'Stres'};
const AUTOSAVE_URL = "{{ route('screening.autosave') }}";
const LEAVE_URL    = "{{ route('screening.leave') }}";
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let current            = 0;
let answers            = @json($savedAnswers ?? []);
let isCompletingMissing = false;

const firstUnansweredIndex = QUESTIONS.findIndex(q => answers[q.id] === undefined || answers[q.id] === null);
current = firstUnansweredIndex !== -1 ? firstUnansweredIndex : QUESTIONS.length - 1;

function render() {
    const q   = QUESTIONS[current];
    const pct = ((current + 1) / QUESTIONS.length * 100).toFixed(0);

    document.getElementById('current-num').textContent    = current + 1;
    document.getElementById('question-text').textContent  = q.teks_id;
    document.getElementById('question-en').textContent    = q.teks_en;
    document.getElementById('category-label').textContent = LABEL_MAP[q.subskala] || q.subskala;
    document.getElementById('pct-label').textContent      = pct + '% selesai';
    document.getElementById('progress-bar').style.width   = pct + '%';
    document.getElementById('btn-prev').disabled          = current === 0;
    document.getElementById('btn-next').textContent       = current === QUESTIONS.length - 1 ? 'Kirim Jawaban' : 'Next';

    document.querySelectorAll('.option-btn').forEach(btn => {
        const val = parseInt(btn.dataset.value);
        const sel = answers[q.id] === val;
        btn.classList.toggle('border-blue-500', sel);
        btn.classList.toggle('bg-blue-50',      sel);
        btn.classList.toggle('border-slate-100',!sel);
        btn.querySelector('.check-circle').classList.toggle('border-blue-500', sel);
        btn.querySelector('.check-dot').classList.toggle('hidden', !sel);
    });
}

// ── OPTIMIZED: pindah soal dulu, autosave di background ──────────
function selectAnswer(val) {
    const q = QUESTIONS[current];
    answers[q.id] = val;
    render();

    // Pindah soal langsung tanpa nunggu autosave
    setTimeout(() => {
        if (isCompletingMissing) {
            const nextMissingIndex = QUESTIONS.findIndex(q => answers[q.id] === undefined);
            if (nextMissingIndex !== -1) {
                current = nextMissingIndex;
                render();
                return;
            }
            isCompletingMissing = false;
            current = QUESTIONS.length - 1;
            render();
            return;
        }
        if (current < QUESTIONS.length - 1) {
            current++;
            render();
        }
    }, 200);

    // Autosave jalan di background — tidak block navigasi
    autosaveAnswer(q.id, val).then(expired => {
        if (expired) return;
    });
}

function nextQuestion() {
    if (current < QUESTIONS.length - 1) {
        current++;
        render();
        return;
    }
    submitForm();
}

function prevQuestion() {
    if (current > 0) {
        current--;
        render();
    }
}

function confirmExit(event, url) {
    event.preventDefault();
    Swal.fire({
        icon: 'info',
        title: 'Keluar dari screening?',
        text: 'Progress screening kamu akan tetap disimpan sementara. Timer akan mulai berjalan setelah kamu keluar.',
        showCancelButton: true,
        confirmButtonText: 'Ya, keluar',
        cancelButtonText: 'Lanjut screening',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        background: '#ffffff',
        color: '#0f172a',
        customClass: { popup:'rounded-3xl', confirmButton:'rounded-xl px-5 py-2', cancelButton:'rounded-xl px-5 py-2' }
    }).then(result => {
        if (result.isConfirmed) {
            notifyLeaveScreening();
            setTimeout(() => window.location.href = url, 150);
        }
    });
}

function notifyLeaveScreening() {
    const payload = JSON.stringify({ leaving: true });

    fetch(LEAVE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: payload,
        keepalive: true,
    }).catch(() => {});
}

window.addEventListener('pagehide', function () {
    notifyLeaveScreening();
});

function submitForm() {
    const missing = QUESTIONS.filter(q => answers[q.id] === undefined);

    if (missing.length) {
        Swal.fire({
            icon: 'warning',
            title: 'Screening belum lengkap',
            text: 'Masih ada ' + missing.length + ' pertanyaan yang belum dijawab. Kamu akan diarahkan ke pertanyaan yang belum terisi.',
            confirmButtonText: 'Oke, lengkapi',
            confirmButtonColor: '#2563eb',
            background: '#ffffff',
            color: '#0f172a',
            customClass: { popup:'rounded-3xl', confirmButton:'rounded-xl px-5 py-2' }
        }).then(result => {
            if (result.isConfirmed) {
                isCompletingMissing = true;
                current = QUESTIONS.indexOf(missing[0]);
                render();
            }
        });
        return;
    }

    Swal.fire({
        icon: 'question',
        title: 'Kirim jawaban screening?',
        text: 'Setelah dikirim, jawaban tidak bisa diubah.',
        showCancelButton: true,
        confirmButtonText: 'Ya, kirim',
        cancelButtonText: 'Cek lagi',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        background: '#ffffff',
        color: '#0f172a',
        customClass: { popup:'rounded-3xl', confirmButton:'rounded-xl px-5 py-2', cancelButton:'rounded-xl px-5 py-2' }
    }).then(result => {
        if (result.isConfirmed) {
            const hidden = document.getElementById('hidden-answers');
            hidden.innerHTML = '';
            QUESTIONS.forEach(q => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'answers[' + q.id + ']';
                inp.value = answers[q.id];
                hidden.appendChild(inp);
            });
            document.getElementById('screening-form').submit();
        }
    });
}

function autosaveAnswer(questionNumber, value) {
    return fetch(AUTOSAVE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ question_number: questionNumber, value: value })
    })
    .then(async response => {
        const result = await response.json().catch(() => null);
        if (response.status === 419 || result?.expired) {
            showExpiredModal(
                result?.message  || 'Sesi telah berakhir, mohon ulangi screening.',
                result?.redirect || "{{ route('patient.dashboard') }}"
            );
            return true;
        }
        return false;
    })
    .catch(() => {
        console.log('Autosave gagal. Jawaban tetap tersimpan sementara di browser.');
        return false;
    });
}

function showExpiredModal(message, redirectUrl) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 px-4';
    modal.innerHTML = `
        <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full p-10 text-center">
            <div class="w-28 h-28 mx-auto mb-6 rounded-full border-4 border-sky-400 flex items-center justify-center">
                <span class="text-sky-400 text-6xl font-light">i</span>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 mb-5">Sesi telah berakhir</h2>
            <p class="text-slate-600 text-lg leading-relaxed mb-8">${message}</p>
            <button type="button" id="expired-ok-btn"
                    class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition">
                Mulai ulang screening
            </button>
        </div>`;
    document.body.appendChild(modal);
    document.getElementById('expired-ok-btn').addEventListener('click', () => {
        window.location.href = redirectUrl;
    });
}

render();
</script>
</body>
</html>