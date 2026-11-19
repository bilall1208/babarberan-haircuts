<link rel="stylesheet" href="{{ asset('css/babarberan.css') }}">
<main class="container" style="max-width:640px;padding-block:48px;">
    <h1 style="margin-bottom:24px;">Transaksi Baru</h1>

    @error('items') <div class="toast toast--error" style="margin-bottom:20px;">{{ $message }}</div> @enderror

    <form method="POST" action="{{ route('admin.transaksi.store') }}" style="display:flex;flex-direction:column;gap:20px;">
        @csrf

        <div class="field">
            <label class="field__label">Tanggal</label>
            <input class="field__control" type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}">
        </div>

        <div id="rows" style="display:flex;flex-direction:column;gap:12px;">
            <div class="row" style="display:flex;gap:12px;">
                <div class="select" style="flex:1;">
                    <select class="field__control" name="items[0][layanan_id]">
                        @foreach ($layanan as $l)
                            <option value="{{ $l->id }}">{{ $l->nama }} - Rp {{ number_format($l->harga, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <input class="field__control" type="number" name="items[0][qty]" value="1" min="1" style="width:90px;">
            </div>
        </div>

        <div style="display:flex;gap:12px;">
            <button type="button" id="add" class="btn btn--secondary">Tambah layanan</button>
            <button type="submit" class="btn btn--primary">Simpan</button>
        </div>
    </form>
</main>

<script>
    let i = 1;
    document.getElementById('add').addEventListener('click', () => {
        const rows = document.getElementById('rows');
        const row = rows.firstElementChild.cloneNode(true);
        row.querySelector('select').name = `items[${i}][layanan_id]`;
        row.querySelector('input').name = `items[${i}][qty]`;
        row.querySelector('input').value = 1;
        rows.appendChild(row);
        i++;
    });
</script>