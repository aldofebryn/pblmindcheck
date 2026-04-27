@forelse($questions as $q)
<tr class="hover:bg-slate-50 transition">
    <td class="px-6 py-5 whitespace-nowrap font-mono text-sm font-semibold text-slate-700">
        {{ str_pad($q->nomor, 2, '0', STR_PAD_LEFT) }}
    </td>
    <td class="px-6 py-5 text-slate-800 align-top">{{ $q->teks_id }}</td>
    <td class="px-6 py-5 italic text-slate-500 align-top">{{ $q->teks_en }}</td>
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
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.questions.edit', $q->id) }}" 
               class="text-blue-600 hover:text-blue-800 font-medium transition">Edit</a>
            <button type="button" onclick="confirmDelete({{ $q->id }})" 
                    class="text-red-600 hover:text-red-800 font-medium transition">Hapus</button>
            <form id="delete-form-{{ $q->id }}" action="{{ route('admin.questions.destroy', $q->id) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Tidak ada pertanyaan</td>
</tr>
@endforelse

<script>
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus pertanyaan ini? Data akan dipindahkan ke tong sampah.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>