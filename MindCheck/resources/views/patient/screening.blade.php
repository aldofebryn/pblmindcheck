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

{{-- Header --}}
<header class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40" style="height:60px">
    <div class="w-full px-6 lg:px-14 xl:px-24 h-full flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <span class="font-bold text-slate-900 text-lg">Skrining DASS-21</span>
        </div>

        <a href="{{ route('patient.dashboard') }}"
           onclick="confirmExit(event, this.href)"
           class="px-5 py-2.5 text-slate-500 hover:text-slate-700 text-base font-semibold rounded-xl hover:bg-slate-100 transition">
            Keluar
        </a>
    </div>
</header>

<main class="flex-1 flex flex-col w-full px-6 lg:px-14 xl:px-24" id="app">
    <form method="POST" action="{{ route('screening.submit') }}" id="screening-form">
        @csrf
        <div id="hidden-answers"></div>

        {{-- Progress --}}
        <div class="pt-12 pb-8">
            <div class="flex items-end justify-between mb-4">
                <div>
                    <span id="current-num" class="font-bold text-blue-600 text-4xl lg:text-5xl">1</span>
                    <span class="text-slate-400 font-medium ml-2 text-2xl"> / {{ $questions->count() }}</span>
                </div>
                <div class="text-right">
                    <span id="category-label" class="text-sm lg:text-base font-bold text-slate-400 uppercase tracking-wider"></span>
                    <span id="pct-label" class="block text-xs lg:text-sm text-slate-300 mt-1"></span>
                </div>
            </div>

            <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
                <div id="progress-bar"
                     class="h-full bg-blue-600 rounded-full transition-all duration-500"
                     style="width:0%"></div>
            </div>
        </div>

        {{-- Kartu soal --}}
        <div id="question-card"
             class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 lg:p-12 mb-10">

            <p id="question-en"
               class="text-slate-400 font-medium italic mb-4 text-base lg:text-lg"></p>

            <h2 id="question-text"
                class="font-bold text-slate-900 leading-snug 
                       text-xl sm:text-2xl lg:text-3xl xl:text-4xl">
            </h2>
        </div>

        {{-- Pilihan --}}
        <div class="grid sm:grid-cols-2 gap-5" id="options">
            @php
            $opsi = [
                [0,'Tidak pernah',   'Tidak berlaku sama sekali untuk saya'],
                [1,'Kadang-kadang',  'Berlaku untuk saya sesekali'],
                [2,'Sering',         'Berlaku dalam kadar yang cukup besar'],
                [3,'Sangat sering',  'Sangat berlaku, hampir sepanjang waktu'],
            ];
            @endphp

            @foreach($opsi as [$val,$label,$desc])
            <button type="button"
                    data-value="{{ $val }}"
                    onclick="selectAnswer({{ $val }})"
                    class="option-btn flex items-start gap-4 p-5 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-300 hover:bg-blue-50 transition-all text-left group">

                <span class="w-8 h-8 rounded-full border-2 border-slate-200 group-hover:border-blue-400 flex items-center justify-center shrink-0 check-circle transition-colors">
                    <span class="w-4 h-4 rounded-full bg-blue-600 hidden check-dot"></span>
                </span>

                <div>
                    <p class="font-bold text-slate-900 text-base">{{ $label }}</p>
                    <p class="text-slate-400 mt-0.5 text-sm">{{ $desc }}</p>
                </div>
            </button>
            @endforeach
        </div>

        {{-- Nav --}}
        <div class="flex justify-between items-center mt-10 pb-12 gap-4">
<button type="button" onclick="prevQuestion()" id="btn-prev"
        class="flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-0 transition text-base font-semibold">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 19l-7-7 7-7"/>
    </svg>
    Kembali
</button>

            <span class="text-slate-400 text-sm lg:text-base hidden sm:block">
                Progress tersimpan otomatis
            </span>

            <button type="button"
                    onclick="nextQuestion()"
                    id="btn-next"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                Next
            </button>
        </div>
    </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const QUESTIONS = @json($questionsFormatted);
const LABEL_MAP = {Depression:'Depresi', Anxiety:'Kecemasan', Stress:'Stres'};

let current = 0;
let answers = @json($savedAnswers ?? []);
let isCompletingMissing = false;

const AUTOSAVE_URL = "{{ route('screening.autosave') }}";
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const firstUnansweredIndex = QUESTIONS.findIndex(q => answers[q.id] === undefined || answers[q.id] === null);

if (firstUnansweredIndex !== -1) {
    current = firstUnansweredIndex;
} else {
    current = QUESTIONS.length - 1;
}

function render() {
    const q = QUESTIONS[current];
    const pct = ((current + 1) / QUESTIONS.length * 100).toFixed(0);

    document.getElementById('current-num').textContent = current + 1;
    document.getElementById('question-text').textContent = q.teks_id;
    document.getElementById('question-en').textContent = q.teks_en;
    document.getElementById('category-label').textContent = LABEL_MAP[q.subskala] || q.subskala;
    document.getElementById('pct-label').textContent = pct + '% selesai';
    document.getElementById('progress-bar').style.width = pct + '%';

    document.getElementById('btn-prev').disabled = current === 0;

    const btnNext = document.getElementById('btn-next');
    btnNext.textContent = current === QUESTIONS.length - 1 ? 'Kirim Jawaban' : 'Next';

    document.querySelectorAll('.option-btn').forEach(btn => {
        const val = parseInt(btn.dataset.value);
        const sel = answers[q.id] === val;

        btn.classList.toggle('border-blue-500', sel);
        btn.classList.toggle('bg-blue-50', sel);
        btn.classList.toggle('border-slate-100', !sel);

        btn.querySelector('.check-circle').classList.toggle('border-blue-500', sel);
        btn.querySelector('.check-dot').classList.toggle('hidden', !sel);
    });
}

function selectAnswer(val) {
    answers[QUESTIONS[current].id] = val;
    render();

    autosaveAnswer(QUESTIONS[current].id, val).then(expired => {
        if (expired) {
            return;
        }

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
        }, 300);
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
        text: 'Progress screening kamu akan tetap disimpan sementara.',
        showCancelButton: true,
        confirmButtonText: 'Ya, keluar',
        cancelButtonText: 'Lanjut screening',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        background: '#ffffff',
        color: '#0f172a',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-5 py-2',
            cancelButton: 'rounded-xl px-5 py-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

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
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-5 py-2'
        }
    }).then((result) => {
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
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-5 py-2',
            cancelButton: 'rounded-xl px-5 py-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const hidden = document.getElementById('hidden-answers');
            hidden.innerHTML = '';

            QUESTIONS.forEach(q => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'answers[' + q.id + ']';
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
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            question_number: questionNumber,
            value: value,
        })
    })
    .then(async response => {
        const result = await response.json().catch(() => null);

        if (response.status === 419 || result?.expired) {
            showExpiredModal(result?.message || 'Sesi telah berakhir, mohon ulangi screening.', result?.redirect || "{{ route('patient.dashboard') }}");
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

            <h2 class="text-3xl font-bold text-slate-900 mb-5">
                Sesi telah berakhir
            </h2>

            <p class="text-slate-600 text-lg leading-relaxed mb-8">
                ${message}
            </p>

            <button type="button"
                    id="expired-ok-btn"
                    class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition">
                Mulai ulang screening
            </button>
        </div>
    `;

    document.body.appendChild(modal);

    document.getElementById('expired-ok-btn').addEventListener('click', function () {
        window.location.href = redirectUrl;
    });
}

render();
</script>
</body>
</html>