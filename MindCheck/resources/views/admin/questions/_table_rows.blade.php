@forelse($questions as $q)
<tr class="hover:bg-slate-50 transition">
    <td class="px-6 py-5 whitespace-nowrap font-mono text-sm font-semibold text-slate-700">
        {{ str_pad($q->nomor, 2, '0', STR_PAD_LEFT) }}
    </td>
    
    <td class="px-6 py-5 text-slate-800 align-top font-serif font-normal">
        {{ $q->teks_id }}
    </td>
    <td class="px-6 py-5 text-slate-500 align-top font-serif font-normal italic">
        {{ $q->teks_en }}
    </td>
    
    <td class="px-6 py-5 whitespace-nowrap align-top">
        @php
            $color = match($q->subskala) {
                'Depression' => 'blue',
                'Anxiety'    => 'violet',
                'Stress'     => 'orange',
                default      => 'gray'
            };
            $label = match($q->subskala) {
                'Depression' => 'Depresi',
                'Anxiety'    => 'Kecemasan',
                'Stress'     => 'Stres',
                default      => $q->subskala
            };
        @endphp
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
            {{ $label }}
        </span>
    </td>
    <td class="px-6 py-5 whitespace-nowrap text-right align-top">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.questions.edit', $q->id) }}"
               class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
               title="Edit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>

            <form id="delete-q-{{ $q->id }}" action="{{ route('admin.questions.destroy', $q->id) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>

            <button type="button"
                    onclick="window.openModal_deleteQuestion('delete-q-{{ $q->id }}')"
                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                    title="Hapus">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Tidak ada pertanyaan</td>
</tr>
@endforelse