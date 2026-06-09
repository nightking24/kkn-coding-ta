<?php

namespace App\Exports;

use App\Models\Kelompok;
use App\Models\Periode;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PesertaExport implements WithEvents
{
    protected $periode_id;

    // Constructor untuk menerima ID periode yang akan diexport
    public function __construct($periode_id)
    {
        // Menyimpan ID periode ke property class
        $this->periode_id = $periode_id;
    }

    // Mendaftarkan event yang dijalankan setelah sheet Excel selesai dibuat
    public function registerEvents(): array
    {
        return [

                // Event AfterSheet dijalankan setelah worksheet berhasil dibuat
            AfterSheet::class => function (AfterSheet $event) {

                // Mengambil worksheet aktif yang akan dimodifikasi
                $sheet = $event->sheet->getDelegate();

                // ======================================
                // DATA PERIODE
                // ======================================
    
                // Mengambil data periode berdasarkan ID periode yang dipilih
                $periode = Periode::find($this->periode_id);

                // ======================================
                // JUDUL
                // ======================================
    
                $sheet->mergeCells('A1:L1');

                $sheet->setCellValue(
                    'A1',
                    'LAPORAN KELOMPOK KKN REGULER'
                );

                $sheet->getStyle('A1')->applyFromArray([
                    // Mengatur font judul
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],

                    // Mengatur posisi judul di tengah
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // ======================================
                // INFORMASI KKN
                // ======================================
    
                // Menggabungkan baris informasi KKN
                $sheet->mergeCells('A2:L2');
                $sheet->mergeCells('A3:L3');
                $sheet->mergeCells('A4:L4');

                $sheet->setCellValue(
                    'A2',
                    'Nama KKN : ' . ($periode->nama_kkn ?? '-')
                );

                $sheet->setCellValue(
                    'A3',
                    'Tahun : ' . ($periode->tahun_kkn ?? '-')
                );

                $sheet->setCellValue(
                    'A4',
                    'Lokasi : ' . ($periode->lokasi ?? '-')
                );

                // Mengatur style informasi KKN
                $sheet->getStyle('A2:A4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                $row = 6;

                // ======================================
                // DATA KELOMPOK
                // ======================================
    
                // Mengambil seluruh kelompok beserta relasinya
                $kelompok = Kelompok::with([
                    'peserta',
                    'dpl',
                    'apl',
                    'tuanRumah'
                ])
                    // Mengambil seluruh kelompok beserta relasinya
                    ->where('id_periode', $this->periode_id)

                    // Mengurutkan berdasarkan nomor kelompok
                    ->orderBy('nomor_kelompok')

                    // Mengambil seluruh hasil query
                    ->get();

                // Memproses setiap kelompok
                foreach ($kelompok as $k) {

                    // Menyimpan posisi awal kelompok
                    $startRow = $row;

                    // ======================================
                    // HEADER KOLOM
                    // ======================================
    
                    $headers = [
                        'A' => 'Kelompok',
                        'B' => 'No',
                        'C' => 'NIM',
                        'D' => 'Nama Lengkap',
                        'E' => 'Prodi',
                        'F' => 'Gender',
                        'G' => 'DPL',
                        'H' => 'Kontak DPL',
                        'I' => 'APL',
                        'J' => 'Kontak APL',
                        'K' => 'Desa',
                        'L' => 'Dusun',
                    ];

                    // Menampilkan seluruh header ke worksheet
                    foreach ($headers as $col => $text) {

                        // Menuliskan teks header pada kolom tertentu
                        $sheet->setCellValue($col . $row, $text);

                        // Mengatur tampilan header tabel
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 10,
                            ],

                            // Posisi teks di tengah
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],

                            // Memberi warna latar belakang biru muda
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'B7DEE8'
                                ]
                            ],

                            // Memberi garis border
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                ]
                            ]
                        ]);
                    }

                    $row++;

                    // ======================================
                    // DATA PESERTA
                    // ======================================
    
                    // Mengambil seluruh peserta pada kelompok
                    $peserta = $k->peserta;

                    // Menentukan jumlah peserta minimum 1
                    $jumlahPeserta = max($peserta->count(), 1);

                    // Menentukan posisi akhir kelompok
                    $endRow = $row + $jumlahPeserta - 1;

                    // ======================================
                    // MERGE CELL
                    // ======================================
    
                    // Kolom yang akan digabungkan untuk informasi kelompok
                    $mergeCols = ['A', 'G', 'H', 'I', 'J', 'K', 'L'];

                    // Menggabungkan sel sesuai jumlah peserta
                    foreach ($mergeCols as $col) {
                        $sheet->mergeCells($col . $row . ':' . $col . $endRow);
                    }

                    // ======================================
                    // ISI INFO KELOMPOK
                    // ======================================
    
                    // nomor kelompok
                    $sheet->setCellValue("A{$row}", $k->nomor_kelompok);

                    // DPL
                    $sheet->setCellValue(
                        "G{$row}",
                        optional($k->dpl)->nama
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        optional($k->dpl)->no_telp
                    );

                    // APL
                    $sheet->setCellValue(
                        "I{$row}",
                        optional($k->apl)->nama
                    );

                    $sheet->setCellValue(
                        "J{$row}",
                        optional($k->apl)->no_telp
                    );

                    // lokasi
                    $sheet->setCellValue("K{$row}", $k->desa);

                    $sheet->setCellValue("L{$row}", $k->dusun);

                    // Mencegah nomor telepon APL berubah menjadi scientific notation
                    $sheet->getStyle("H{$row}:H{$endRow}")
                        ->getNumberFormat()
                        ->setFormatCode('0');

                    // Mencegah nomor telepon APL berubah menjadi scientific notation
                    $sheet->getStyle("J{$row}:J{$endRow}")
                        ->getNumberFormat()
                        ->setFormatCode('0');

                    // ======================================
                    // STYLE INFO TENGAH
                    // ======================================
    
                    $sheet->getStyle("A{$row}:L{$endRow}")
                        ->applyFromArray([
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                            'font' => [
                                'size' => 11
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                ]
                            ]
                        ]);

                    // ======================================
                    // ISI PESERTA
                    // ======================================
    
                    // Mencegah nomor telepon APL berubah menjadi scientific notation
                    foreach ($peserta as $index => $p) {

                        // Menentukan posisi baris peserta saat ini
                        $currentRow = $row + $index;

                        // Menampilkan nomor urut peserta
                        $sheet->setCellValue("B{$currentRow}", $index + 1);

                        $sheet->setCellValue("C{$currentRow}", $p->nim);

                        $sheet->setCellValue("D{$currentRow}", $p->nama);

                        $sheet->setCellValue("E{$currentRow}", $p->prodi);

                        $sheet->setCellValue(
                            "F{$currentRow}",
                            in_array($p->gender, ['L', 'Pria'])
                            ? 'Pria'
                            : 'Wanita'
                        );

                        // Mencegah NIM berubah menjadi scientific notation
                        $sheet->getStyle("C{$currentRow}")
                            ->getNumberFormat()
                            ->setFormatCode('0');
                        $sheet->getStyle("B{$currentRow}:F{$currentRow}")
                            ->applyFromArray([
                                'alignment' => [
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'wrapText' => true,
                                ],
                                'font' => [
                                    'size' => 9
                                ],
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => Border::BORDER_THIN,
                                    ]
                                ]
                            ]);

                        // Align No column to center
                        $sheet->getStyle("B{$currentRow}")
                            ->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                ]
                            ]);

                        // Align NIM column to center
                        $sheet->getStyle("C{$currentRow}")
                            ->applyFromArray([
                                'alignment' => [
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                                ]
                            ]);
                    }

                    // ======================================
                    // TINGGI ROW
                    // ======================================
    
                    // Mengatur tinggi setiap baris kelompok
                    for ($r = $startRow; $r <= $endRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(30);
                    }

                    // ======================================
                    // BORDER LUAR
                    // ======================================
    
                    // Memberikan border tebal di luar area kelompok
                    $sheet->getStyle("A{$startRow}:L{$endRow}")
                        ->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_MEDIUM,
                                ]
                            ]
                        ]);

                    // Memberikan jarak 4 baris sebelum kelompok berikutnya
                    $row = $endRow + 4;
                }

                // Mencegah seluruh kolom NIM menjadi scientific notation
                $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('H:H')->getNumberFormat()->setFormatCode('0');
                $sheet->getStyle('J:J')->getNumberFormat()->setFormatCode('0');

                // ======================================
                // AUTO SIZE
                // ======================================
    
                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)
                        ->setAutoSize(true);
                }

                // Mengatur lebar kolom nama secara khusus
                $sheet->getColumnDimension('C')->setWidth(35);

                // Mengatur tampilan zoom Excel menjadi 85%
                $sheet->getSheetView()->setZoomScale(85);
            }
        ];
    }
}