<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilUsaha;
use App\Models\TransaksiDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.laporan.index', $this->data($request));
    }

    public function cetak(Request $request)
    {
        return view('admin.laporan.cetak', $this->data($request) + ['profil' => ProfilUsaha::current()]);
    }

    public function pdf(Request $request)
    {
        $d      = $this->data($request);
        $profil = ProfilUsaha::current();

        $pdf = Pdf::loadView('admin.laporan.pdf', $d + [
            'profil'   => $profil,
            'logoPath' => $this->logoPath($profil),
        ])->setPaper('a4', 'portrait');

        // render dulu, lalu tambahkan footer + nomor halaman di setiap halaman
        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font   = $dompdf->getFontMetrics()->get_font('helvetica');

        $canvas->page_text(40, 815, ($profil?->nama_usaha ?? 'Babarberan Haircuts') . ' · Laporan', $font, 8, [0.4, 0.4, 0.4]);
        $canvas->page_text(500, 815, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', $font, 8, [0.4, 0.4, 0.4]);

        return $pdf->download($this->namaFile($d, 'pdf'));
    }

        public function excel(Request $request)
    {
        $d      = $this->data($request);
        $profil = ProfilUsaha::current();

        $ss = new Spreadsheet();
        $sh = $ss->getActiveSheet();
        $sh->setTitle('Laporan');

        // ---- Judul dan info filter ----
        $sh->setCellValue('A1', Str::upper($profil?->nama_usaha ?? 'Babarberan Haircuts'));
        $sh->setCellValue('A2', 'Laporan Transaksi');
        $sh->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sh->getStyle('A2')->getFont()->setSize(12);

        $info = [
            ['Periode',       $d['periodeLong']],
            ['Layanan',       $d['layananDipilih'] ?? 'Semua layanan'],
            ['Tanggal cetak', now()->translatedFormat('d F Y H:i')],
        ];
        foreach ($info as $i => [$label, $nilai]) {
            $r = 4 + $i;
            $sh->setCellValue("A{$r}", $label);
            $sh->setCellValue("B{$r}", $nilai);
            $sh->getStyle("A{$r}")->getFont()->setBold(true);
        }

        // ---- Tabel rincian ----
        $header = 12;
        $first  = $header + 1;

        foreach (['ID', 'Tanggal', 'Layanan', 'Harga', 'Jumlah', 'Total', 'Metode'] as $i => $judul) {
            $sh->setCellValue(chr(65 + $i) . $header, $judul);
        }

        $r = $first;
        foreach ($d['baris'] as $b) {
            $sh->setCellValue("A{$r}", 'TRX-' . str_pad($b->transaksi_id, 3, '0', STR_PAD_LEFT));
            $sh->setCellValue("B{$r}", Date::PHPToExcel(Carbon::parse($b->tgl)->toDateTime()));
            $sh->setCellValue("C{$r}", $b->nama_layanan);
            $sh->setCellValue("D{$r}", (int) $b->harga);
            $sh->setCellValue("E{$r}", (int) $b->qty);
            $sh->setCellValue("F{$r}", (int) $b->subtotal);
            $sh->setCellValue("G{$r}", $d['labelMetode'][$b->metode] ?? $b->metode);
            $r++;
        }

        $last     = $r - 1;
        $totalRow = $r;
        $adaData  = $d['baris']->isNotEmpty();

        $sh->setCellValue("A{$totalRow}", 'TOTAL');
        $sh->setCellValue("E{$totalRow}", $adaData ? "=SUM(E{$first}:E{$last})" : 0);
        $sh->setCellValue("F{$totalRow}", $adaData ? "=SUM(F{$first}:F{$last})" : 0);

        // ---- Ringkasan ----
        $sh->setCellValue('A8',  'Total pendapatan');
        $sh->setCellValue('B8',  "=F{$totalRow}");
        $sh->setCellValue('A9',  'Total transaksi');
        $sh->setCellValue('B9',  (int) $d['totalTransaksi']);
        $sh->setCellValue('A10', 'Unit layanan');
        $sh->setCellValue('B10', "=E{$totalRow}");
        $sh->getStyle('A8:A10')->getFont()->setBold(true);
        $sh->getStyle('B8:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // ---- Ringkasan per metode ----
        $baris = 8;
        foreach ($d['perMetode'] as $m) {
            $sh->setCellValue("D{$baris}", $m['label']);
            $sh->setCellValue("E{$baris}", $m['jml'] . ' trx');
            $sh->setCellValue("F{$baris}", $m['total']);
            $baris++;
        }
        $sh->getStyle('D8:D10')->getFont()->setBold(true);
        $sh->getStyle('E8:E10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ---- Format ----
        $rupiah = '"Rp"#,##0';
        $sh->getStyle("D{$first}:D{$totalRow}")->getNumberFormat()->setFormatCode($rupiah);
        $sh->getStyle("F{$first}:F{$totalRow}")->getNumberFormat()->setFormatCode($rupiah);
        $sh->getStyle('F8:F10')->getNumberFormat()->setFormatCode($rupiah);
        $sh->getStyle('B8')->getNumberFormat()->setFormatCode($rupiah);
        $sh->getStyle("B{$first}:B{$last}")->getNumberFormat()->setFormatCode('dd mmm yyyy');
        $sh->getStyle("B{$first}:B{$last}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sh->getStyle("E{$header}:E{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sh->getStyle("A{$header}:G{$header}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111111']],
        ]);
        $sh->getStyle("A{$header}:G{$totalRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D1D1');
        $sh->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
        ]);

        foreach (['A' => 12, 'B' => 16, 'C' => 28, 'D' => 16, 'E' => 10, 'F' => 18, 'G' => 12] as $kolom => $lebar) {
            $sh->getColumnDimension($kolom)->setWidth($lebar);
        }

        $sh->freezePane('A' . $first);
        if ($adaData) {
            $sh->setAutoFilter("A{$header}:G{$last}");
        }

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $this->namaFile($d, 'xlsx'), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function namaFile(array $d, string $ekstensi): string
    {
        $nama = 'laporan-transaksi_' . $d['dari']->format('Ymd') . '-' . $d['sampai']->format('Ymd');

        if ($d['layananDipilih']) {
            $nama .= '_' . Str::slug($d['layananDipilih']);
        }

        return $nama . '.' . $ekstensi;
    }

    /** dompdf hanya mendukung PNG/JPG; kembalikan null kalau logo tidak ada atau formatnya lain. */
    private function logoPath(?ProfilUsaha $profil): ?string
    {
        if (! $profil?->logo) {
            return null;
        }

        $path = storage_path('app/public/' . $profil->logo);

        return is_file($path) && preg_match('/\.(png|jpe?g)$/i', $path)
            ? str_replace('\\', '/', $path)
            : null;
    }

    private function data(Request $request): array
    {
        // Bawaan: 30 hari terakhir
        $dari   = $this->tanggal($request->query('dari'), now()->subDays(29));
        $sampai = $this->tanggal($request->query('sampai'), now());

        if ($dari->gt($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $layananDipilih = trim((string) $request->query('layanan', '')) ?: null;

        $query = TransaksiDetail::query()
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->whereBetween('transaksi.tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->when($layananDipilih, fn($q) => $q->where('transaksi_detail.nama_layanan', $layananDipilih));

        $totalPendapatan = (clone $query)->sum('transaksi_detail.subtotal');
        $totalTransaksi  = (clone $query)->distinct()->count('transaksi_detail.transaksi_id');
        $totalUnit       = (clone $query)->sum('transaksi_detail.qty');

        $baris = (clone $query)
            ->select('transaksi_detail.*', 'transaksi.tanggal as tgl', 'transaksi.metode_bayar as metode')
            ->orderBy('transaksi.tanggal')
            ->orderBy('transaksi_detail.id')
            ->get();

        // Ringkasan per metode (dijumlahkan dari subtotal baris, jadi tidak ada hitungan ganda)
        $labelMetode = ['tunai' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'];

        $mentah = (clone $query)
            ->selectRaw('transaksi.metode_bayar as metode, SUM(transaksi_detail.subtotal) as total, COUNT(DISTINCT transaksi_detail.transaksi_id) as jml')
            ->groupBy('transaksi.metode_bayar')
            ->get()->keyBy('metode');

        $perMetode = collect($labelMetode)->map(fn($label, $kode) => [
            'label' => $label,
            'total' => (int) ($mentah->get($kode)?->total ?? 0),
            'jml'   => (int) ($mentah->get($kode)?->jml ?? 0),
        ]);

        $daftarLayanan = TransaksiDetail::query()->orderBy('nama_layanan')->distinct()->pluck('nama_layanan');

        // Catatan untuk nama layanan yang punya lebih dari satu harga
        $catatan = $baris->groupBy('nama_layanan')
            ->map(fn($g) => $g->pluck('harga')->unique()->sort()->values())
            ->filter(fn($h) => $h->count() > 1)
            ->map(function ($h, $nama) {
                $teks = $h->map(fn($x) => 'Rp' . number_format($x, 0, ',', '.'))->all();

                return $nama . ' dicatat sebagai ' . count($teks) . ' pilihan harga: '
                    . implode(', ', array_slice($teks, 0, -1)) . ' dan ' . end($teks) . '.';
            })->values();

        return [
            'dari' => $dari,
            'sampai' => $sampai,
            'layananDipilih' => $layananDipilih,
            'totalPendapatan' => $totalPendapatan,
            'totalTransaksi' => $totalTransaksi,
            'totalUnit' => $totalUnit,
            'baris' => $baris,
            'daftarLayanan' => $daftarLayanan,
            'catatan' => $catatan,
            'periodeShort' => $this->periode($dari, $sampai, false),
            'periodeLong'  => $this->periode($dari, $sampai, true),
            'perMetode' => $perMetode, 'labelMetode' => $labelMetode,
        ];
    }

    private function tanggal(?string $nilai, Carbon $bawaan): Carbon
    {
        try {
            return $nilai
                ? Carbon::createFromFormat('Y-m-d', $nilai)->startOfDay()
                : $bawaan->copy()->startOfDay();
        } catch (Throwable) {
            return $bawaan->copy()->startOfDay();
        }
    }

    private function periode(Carbon $dari, Carbon $sampai, bool $panjang): string
    {
        $b = $panjang ? 'F' : 'M';

        if ($dari->isSameDay($sampai)) {
            return $sampai->translatedFormat("d $b Y");
        }
        if ($dari->isSameMonth($sampai)) {
            return $dari->format('d') . '–' . $sampai->translatedFormat("d $b Y");
        }
        if ($dari->isSameYear($sampai)) {
            return $dari->translatedFormat("d $b") . ' – ' . $sampai->translatedFormat("d $b Y");
        }

        return $dari->translatedFormat("d $b Y") . ' – ' . $sampai->translatedFormat("d $b Y");
    }
}
