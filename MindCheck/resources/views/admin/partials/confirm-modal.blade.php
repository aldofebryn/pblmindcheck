{{--
    Reusable Glassmorphic Confirm Modal
    ─────────────────────────────────────────────────────────────────────────
    Props (PHP variables diset sebelum @include):
      $modalId        : string  — ID unik modal (default: 'confirmModal')
      $modalTitle     : string  — Judul modal
      $modalBody      : string  — Teks isi (HTML diperbolehkan, pastikan aman)
      $modalWarning   : string  — Teks peringatan di kotak merah (opsional)
      $modalConfirmLabel : string — Label tombol konfirmasi (default: 'Ya, Lanjutkan')
      $modalConfirmColor : string — Warna tombol (red/blue/amber, default: red)
    ─────────────────────────────────────────────────────────────────────────
    Gunakan via JS: window.openModal_<modalId>(formId)
--}}
@php
    $modalId           ??= 'confirmModal';
    $modalTitle        ??= 'Konfirmasi Tindakan';
    $modalBody         ??= 'Apakah Anda yakin ingin melanjutkan?';
    $modalWarning      ??= 'Tindakan ini tidak dapat dibatalkan.';
    $modalConfirmLabel ??= 'Ya, Lanjutkan';
    $modalConfirmColor ??= 'red';
    $btnClass = match($modalConfirmColor) {
        'blue'  => 'bg-blue-600 hover:bg-blue-700',
        'amber' => 'bg-amber-500 hover:bg-amber-600',
        default => 'bg-red-600 hover:bg-red-700',
    };
    $iconPath = match($modalConfirmColor) {
        'blue'  => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'amber' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        default => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    };
    $iconBg = match($modalConfirmColor) {
        'blue'  => 'bg-blue-50 text-blue-500',
        'amber' => 'bg-amber-50 text-amber-500',
        default => 'bg-red-50 text-red-500',
    };
    $warnBg  = match($modalConfirmColor) {
        'blue'  => 'bg-blue-50 border-blue-100 text-blue-700',
        'amber' => 'bg-amber-50 border-amber-100 text-amber-700',
        default => 'bg-red-50 border-red-100 text-red-700',
    };
    $warnIcon = match($modalConfirmColor) {
        'blue'  => 'text-blue-400',
        'amber' => 'text-amber-400',
        default => 'text-red-400',
    };
@endphp

<div id="{{ $modalId }}"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div id="{{ $modalId }}_backdrop"
         class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0"
         onclick="window['closeModal_{{ $modalId }}']()"></div>

    {{-- Panel --}}
    <div id="{{ $modalId }}_panel"
         class="relative bg-white/90 backdrop-blur-xl border border-white/60 rounded-2xl shadow-2xl w-full max-w-md p-7 transition-all duration-200 scale-95 opacity-0">

        {{-- Ikon --}}
        <div class="flex items-center justify-center w-14 h-14 {{ $iconBg }} rounded-2xl mx-auto mb-5">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
            </svg>
        </div>

        {{-- Judul & Isi --}}
        <h3 class="text-xl font-bold text-slate-800 text-center mb-1">{{ $modalTitle }}</h3>
        <div class="text-slate-500 text-sm text-center mb-5">{!! $modalBody !!}</div>

        {{-- Kotak peringatan --}}
        @if($modalWarning)
        <div class="{{ $warnBg }} border rounded-xl px-4 py-3 mb-6 flex gap-3 items-start">
            <svg class="w-5 h-5 {{ $warnIcon }} shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-medium">{{ $modalWarning }}</p>
        </div>
        @endif

        {{-- Tombol --}}
        <div class="flex gap-3">
            <button type="button" onclick="window['closeModal_{{ $modalId }}']();"
                    class="flex-1 px-4 py-2.5 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors text-sm">
                Batal
            </button>
            <button type="button" id="{{ $modalId }}_confirmBtn"
                    class="flex-1 px-4 py-2.5 rounded-xl font-semibold text-white {{ $btnClass }} transition-colors text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                </svg>
                {{ $modalConfirmLabel }}
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const mid      = @json($modalId);
    const modal    = document.getElementById(mid);
    const backdrop = document.getElementById(mid + '_backdrop');
    const panel    = document.getElementById(mid + '_panel');
    const confirmBtn = document.getElementById(mid + '_confirmBtn');
    let _formId    = null;

    window['openModal_' + mid] = function(formId, label) {
        _formId = formId;
        if (label && document.getElementById(mid + '_name')) {
            document.getElementById(mid + '_name').textContent = label;
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            backdrop.classList.replace('opacity-0', 'opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    };

    window['closeModal_' + mid] = function() {
        backdrop.classList.replace('opacity-100', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            _formId = null;
        }, 180);
    };

    confirmBtn.addEventListener('click', function() {
        if (_formId) document.getElementById(_formId).submit();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            window['closeModal_' + mid]();
        }
    });
})();
</script>
