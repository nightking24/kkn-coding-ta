<?php

namespace App\Exports;

use App\Models\Kelompok;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HasilPembagianExport implements WithEvents
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
                // JUDUL
                // ======================================
    
                $sheet->mergeCells('A1:M1');

                $sheet->setCellValue(
                    'A1',
                    'HASIL PEMBAGIAN KELOMPOK KKN REGULER'
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

                $row = 3;

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
                    // HEADER
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
                        'K' => 'Kecamatan',
                        'L' => 'Desa',
                        'M' => 'Dusun',
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

                    $peserta = $k->peserta;

                    $jumlahPeserta = max($peserta->count(), 1);

                    $endRow = $row + $jumlahPeserta - 1;

                    // ======================================
                    // MERGE
                    // ======================================
    
                    $mergeCols = ['A', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

                    foreach ($mergeCols as $col) {
                        $sheet->mergeCells($col . $row . ':' . $col . $endRow);
                    }

                    // ======================================
                    // INFO KELOMPOK
                    // ======================================
    
                    $sheet->setCellValue("A{$row}", $k->nomor_kelompok);

                    $sheet->setCellValue(
                        "G{$row}",
                        optional($k->dpl)->nama
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        optional($k->dpl)->no_telp
                    );

                    $sheet->setCellValue(
                        "I{$row}",
                        optional($k->apl)->nama
                    );

                    $sheet->setCellValue(
                        "J{$row}",
                        optional($k->apl)->no_telp
                    );

                    $sheet->setCellValue("K{$row}", $k->nama_kecamatan);

                    $sheet->setCellValue("L{$row}", $k->desa);

                    $sheet->setCellValue("M{$row}", $k->dusun);

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
                    // PESERTA
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

                    for ($r = $startRow; $r <= $endRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(30);
                    }

                    $sheet->getStyle("A{$startRow}:M{$endRow}")
                        ->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_MEDIUM,
                                ]
                            ]
                        ]);

                    $row = $endRow + 2;
                }

                foreach (range('A', 'M') as $col) {
                    $sheet->getColumnDimension($col)
                        ->setAutoSize(true);
                }

                $sheet->getColumnDimension('D')->setWidth(35);

                $sheet->getSheetView()->setZoomScale(85);
            }
        ];
    }
}