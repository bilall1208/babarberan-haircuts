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

        $transaksi->load('detail');
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

        DB::beginTransaction();                                   // START TRANSACTION
        try {
            $layanan = Layanan::where('aktif', true)->findOrFail($data['layanan_id']);

            // kasir tidak bisa mengubah harga: selalu memakai harga dari daftar layanan
            $harga    = $admin ? $data['harga'] : $layanan->harga;
            $subtotal = $harga * $data['qty'];

            $transaksi = Transaksi::create([
                'kode'    => 'TRX-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'tanggal' => $data['tanggal'],
                'total'   => $subtotal,
                'user_id' => Auth::id(),
            ]);

            $transaksi->detail()->create([
                'layanan_id'   => $layanan->id,
                'nama_layanan' => $layanan->nama,
                'harga'        => $harga,
                'qty'          => $data['qty'],
                'subtotal'     => $subtotal,
            ]);

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

        DB::beginTransaction();
        try {
            $layanan  = Layanan::findOrFail($data['layanan_id']);
            $subtotal = $data['harga'] * $data['qty'];

            $transaksi->update(['tanggal' => $data['tanggal'], 'total' => $subtotal]);

            $transaksi->detail()->delete();
            $transaksi->detail()->create([
                'layanan_id'   => $layanan->id,
                'nama_layanan' => $layanan->nama,
                'harga'        => $data['harga'],
                'qty'          => $data['qty'],
                'subtotal'     => $subtotal,
            ]);

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
        $layananId = $edit?->detail->first()?->layanan_id;

        $layanan = Layanan::where('aktif', true)
            ->when($layananId, fn ($q) => $q->orWhere('id', $layananId))
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

    private function validasi(Request $request): array
    {
        $request->merge(['harga' => preg_replace('/\D/', '', (string) $request->input('harga'))]);

        return $request->validate([
            'tanggal'    => ['required', 'date'],
            'layanan_id' => ['required', 'integer', 'exists:layanan,id'],
            'harga'      => ['required', 'integer', 'min:0'],
            'qty'        => ['required', 'integer', 'min:1', 'max:99'],
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'tanggal.date'        => 'Format tanggal tidak valid.',
            'layanan_id.required' => 'Pilih layanan terlebih dahulu.',
            'layanan_id.exists'   => 'Layanan tidak ditemukan.',
            'harga.required'      => 'Harga satuan wajib diisi.',
            'harga.integer'       => 'Harga satuan harus berupa angka.',
            'qty.required'        => 'Jumlah wajib diisi.',
            'qty.integer'         => 'Jumlah harus berupa angka.',
            'qty.min'             => 'Jumlah minimal 1.',
            'qty.max'             => 'Jumlah maksimal 99.',
        ]);
    }
}