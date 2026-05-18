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

    public function __construct($periode_id)
    {
        $this->periode_id = $periode_id;
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // ======================================
                // DATA PERIODE
                // ======================================
    
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
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // ======================================
                // INFORMASI KKN
                // ======================================
    
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
    
                $kelompok = Kelompok::with([
                    'peserta',
                    'dpl',
                    'apl',
                    'tuanRumah'
                ])
                    ->where('id_periode', $this->periode_id)
                    ->orderBy('nomor_kelompok')
                    ->get();

                foreach ($kelompok as $k) {

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

                    foreach ($headers as $col => $text) {

                        $sheet->setCellValue($col . $row, $text);

                        $sheet->getStyle($col . $row)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 10,
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'B7DEE8'
                                ]
                            ],
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
    
                    $peserta = $k->peserta;

                    $jumlahPeserta = max($peserta->count(), 1);

                    $endRow = $row + $jumlahPeserta - 1;

                    // ======================================
                    // MERGE CELL
                    // ======================================
    
                    $mergeCols = ['A', 'G', 'H', 'I', 'J', 'K', 'L'];

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
    
                    foreach ($peserta as $index => $p) {

                        $currentRow = $row + $index;

                        $sheet->setCellValue("B{$currentRow}", $index + 1);

                        $sheet->setCellValue("C{$currentRow}", $p->nim);

                        $sheet->setCellValue("D{$currentRow}", $p->nama);

                        $sheet->setCellValue("E{$currentRow}", $p->prodi);

                        $sheet->setCellValue(
                            "F{$currentRow}",
                            in_array($p->gender, ['L', 'Pria'])
                            ? 'Laki-Laki'
                            : 'Perempuan'
                        );
                        
                        $sheet->getStyle("B{$currentRow}:F{$currentRow}")
                            ->applyFromArray([
                                'alignment' => [
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                    'horizontal' => Alignment::HORIZONTAL_CENTER,
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
                    }

                    // ======================================
                    // TINGGI ROW
                    // ======================================
    
                    for ($r = $startRow; $r <= $endRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(30);
                    }

                    // ======================================
                    // BORDER LUAR
                    // ======================================
    
                    $sheet->getStyle("A{$startRow}:L{$endRow}")
                        ->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_MEDIUM,
                                ]
                            ]
                        ]);

                    $row = $endRow + 2;
                }

                // ======================================
                // AUTO SIZE
                // ======================================
    
                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)
                        ->setAutoSize(true);
                }

                // khusus nama
                $sheet->getColumnDimension('C')->setWidth(35);

                // zoom
                $sheet->getSheetView()->setZoomScale(85);
            }
        ];
    }
}