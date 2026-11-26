<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\ProfilUsaha;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransaksiController extends Controller
{
    public function index()
    {
        return $this->halaman();
    }

    // route ini dibatasi role:admin
    public function edit(Transaksi $transaksi)
    {
        return $this->halaman($transaksi->load('detail'));
    }

    public function show(Transaksi $transaksi)
    {
        $this->izinkanLihat($transaksi);

        $transaksi->load(['detail', 'user']);
        $totalSemua = $this->lingkup()->sum('total');

        return view('admin.transaksi.show', compact('transaksi', 'totalSemua'));
    }

    public function struk(Transaksi $transaksi)
    {
        $this->izinkanLihat($transaksi);

        $transaksi->load(['detail', 'user']);

        return view('admin.transaksi.struk', [
            'transaksi' => $transaksi,
            'profil'    => ProfilUsaha::current(),
        ]);
    }

    public function store(Request $request)
    {
        $data  = $this->validasi($request);
        $admin = Auth::user()->isAdmin();

        $ids        = collect($data['items'])->pluck('layanan_id')->unique();
        $layananMap = Layanan::where('aktif', true)->whereIn('id', $ids)->get()->keyBy('id');

        if ($layananMap->count() !== $ids->count()) {
            return back()->withInput()
                ->withErrors(['items' => 'Ada layanan yang tidak aktif atau tidak ditemukan.']);
        }

        // kasir tidak bisa mengubah harga: selalu memakai harga dari daftar layanan
        [$baris, $total] = $this->susunBaris($data['items'], $layananMap, $admin);
        $bayar           = $this->pembayaran($data, $total);

        DB::beginTransaction();                                   // START TRANSACTION
        try {
            $transaksi = Transaksi::create([
                'kode'    => 'TRX-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'tanggal' => $data['tanggal'],
                'total'   => $total,
                'user_id' => Auth::id(),
            ] + $bayar);

            foreach ($baris as $b) {
                $transaksi->detail()->create($b);
            }

            DB::commit();                                         // COMMIT
        } catch (Throwable $e) {
            DB::rollBack();                                       // ROLLBACK
            report($e);

            return back()->withInput()
                ->with('error', 'Transaksi gagal disimpan, tidak ada data yang berubah.');
        }

        return redirect()->route('admin.transaksi.show', $transaksi);
    }

    // route ini dibatasi role:admin
    public function update(Request $request, Transaksi $transaksi)
    {
        $data = $this->validasi($request);

        $ids        = collect($data['items'])->pluck('layanan_id')->unique();
        $layananMap = Layanan::whereIn('id', $ids)->get()->keyBy('id');

        [$baris, $total] = $this->susunBaris($data['items'], $layananMap, true);
        $bayar           = $this->pembayaran($data, $total);

        DB::beginTransaction();
        try {
            $transaksi->update(['tanggal' => $data['tanggal'], 'total' => $total] + $bayar);

            $transaksi->detail()->delete();
            foreach ($baris as $b) {
                $transaksi->detail()->create($b);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()
                ->with('error', 'Perubahan gagal disimpan, data lama tidak berubah.');
        }

        return redirect()->route('admin.transaksi.index')->with('success', 'Perubahan berhasil disimpan');
    }

    // route ini dibatasi role:admin
    public function destroy(Transaksi $transaksi)
    {
        try {
            DB::transaction(function () use ($transaksi) {
                $transaksi->detail()->delete();
                $transaksi->delete();
            });
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Transaksi gagal dihapus.');
        }

        return redirect()->route('admin.transaksi.index')->with('success', 'Transaksi berhasil dihapus');
    }

    /** Admin: semua transaksi. Kasir: hanya transaksi yang dia input sendiri. */
    private function lingkup()
    {
        $q = Transaksi::query();

        return Auth::user()->isAdmin() ? $q : $q->where('user_id', Auth::id());
    }

    private function izinkanLihat(Transaksi $transaksi): void
    {
        abort_unless(Auth::user()->isAdmin() || $transaksi->user_id === Auth::id(), 403);
    }

    private function halaman(?Transaksi $edit = null)
    {
        $ids = $edit ? $edit->detail->pluck('layanan_id')->all() : [];

        // saat mengedit, layanan lama yang sudah nonaktif tetap ikut tampil
        $layanan = Layanan::where('aktif', true)
            ->when($ids, fn ($q) => $q->orWhereIn('id', $ids))
            ->orderBy('nama')->orderBy('harga')->get();

        $riwayat         = $this->lingkup()->with('detail')->orderByDesc('tanggal')->orderByDesc('id')->paginate(10);
        $jumlahTransaksi = $this->lingkup()->count();
        $totalSemua      = $this->lingkup()->sum('total');
        $awal            = $this->lingkup()->min('tanggal');
        $akhir           = $this->lingkup()->max('tanggal');

        $totalUnit = TransaksiDetail::whereHas('transaksi', fn ($q) =>
            Auth::user()->isAdmin() ? $q : $q->where('user_id', Auth::id())
        )->sum('qty');

        return view('admin.transaksi.index', compact(
            'edit', 'layanan', 'riwayat', 'jumlahTransaksi', 'totalSemua', 'totalUnit', 'awal', 'akhir'
        ));
    }

    /** Hitung semua baris layanan dan total transaksi. */
    private function susunBaris(array $items, $layananMap, bool $pakaiHargaKiriman): array
    {
        $baris = [];
        $total = 0;

        foreach ($items as $item) {
            $l        = $layananMap[$item['layanan_id']];
            $harga    = $pakaiHargaKiriman ? (int) $item['harga'] : (int) $l->harga;
            $qty      = (int) $item['qty'];
            $subtotal = $harga * $qty;

            $baris[] = [
                'layanan_id'   => $l->id,
                'nama_layanan' => $l->nama,
                'harga'        => $harga,
                'qty'          => $qty,
                'subtotal'     => $subtotal,
            ];
            $total += $subtotal;
        }

        return [$baris, $total];
    }

    /** Tunai: uang diterima harus cukup. QRIS/Transfer: dianggap lunas pas. */
    private function pembayaran(array $data, int $total): array
    {
        if ($data['metode_bayar'] === 'tunai') {
            if (($data['jumlah_bayar'] ?? '') === '') {
                throw ValidationException::withMessages(['jumlah_bayar' => 'Uang diterima wajib diisi.']);
            }

            $bayar = (int) $data['jumlah_bayar'];

            if ($bayar < $total) {
                throw ValidationException::withMessages([
                    'jumlah_bayar' => 'Uang diterima kurang dari total (Rp' . number_format($total, 0, ',', '.') . ').',
                ]);
            }

            return ['metode_bayar' => 'tunai', 'jumlah_bayar' => $bayar, 'kembalian' => $bayar - $total, 'referensi' => null];
        }

        return [
            'metode_bayar' => $data['metode_bayar'],
            'jumlah_bayar' => $total,
            'kembalian'    => 0,
            'referensi'    => $data['referensi'] ?? null,
        ];
    }

    private function validasi(Request $request): array
    {
        $items = collect($request->input('items', []))
            ->filter(fn ($i) => is_array($i))
            ->map(fn ($i) => [
                'layanan_id' => $i['layanan_id'] ?? null,
                'qty'        => preg_replace('/\D/', '', (string) ($i['qty'] ?? '')),
                'harga'      => preg_replace('/\D/', '', (string) ($i['harga'] ?? '')),
            ])->values()->all();

        $request->merge([
            'items'        => $items,
            'jumlah_bayar' => preg_replace('/\D/', '', (string) $request->input('jumlah_bayar')),
        ]);

        return $request->validate([
            'tanggal'            => ['required', 'date'],
            'items'              => ['required', 'array', 'min:1', 'max:20'],
            'items.*.layanan_id' => ['required', 'integer', 'exists:layanan,id'],
            'items.*.qty'        => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.harga'      => ['required', 'integer', 'min:0'],
            'metode_bayar'       => ['required', Rule::in(['tunai', 'qris', 'transfer'])],
            'jumlah_bayar'       => ['nullable', 'integer', 'min:0'],
            'referensi'          => ['nullable', 'string', 'max:50'],
        ], [
            'tanggal.required'            => 'Tanggal wajib diisi.',
            'tanggal.date'                => 'Format tanggal tidak valid.',
            'items.required'              => 'Tambahkan minimal satu layanan.',
            'items.min'                   => 'Tambahkan minimal satu layanan.',
            'items.max'                   => 'Maksimal 20 layanan per transaksi.',
            'items.*.layanan_id.required' => 'Pilih layanan pada baris ini.',
            'items.*.layanan_id.exists'   => 'Layanan tidak ditemukan.',
            'items.*.qty.required'        => 'Jumlah wajib diisi.',
            'items.*.qty.integer'         => 'Jumlah harus berupa angka.',
            'items.*.qty.min'             => 'Jumlah minimal 1.',
            'items.*.qty.max'             => 'Jumlah maksimal 99.',
            'items.*.harga.required'      => 'Harga satuan wajib diisi.',
            'items.*.harga.integer'       => 'Harga satuan harus berupa angka.',
            'metode_bayar.required'       => 'Pilih metode pembayaran.',
            'metode_bayar.in'             => 'Metode pembayaran tidak valid.',
            'referensi.max'               => 'No. referensi maksimal 50 karakter.',
        ]);
    }
}