<?php

namespace App\Exports;

use App\Models\Kelompok;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;



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

                $row = 1;

                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->setCellValue("A{$row}", "KELOMPOK KKN REGULER");
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle("A1:E1000")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $row += 2;

                $kelompok = Kelompok::with(['peserta', 'dpl', 'apl'])
                    ->where('id_periode', $this->periode_id)
                    ->get();

                foreach ($kelompok as $k) {
                    $sheet->setCellValue("A{$row}", "Kelompok K{$k->nomor_kelompok}");
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    $startInfoRow = $row;

                    $sheet->setCellValue("A{$row}", "DPL :");
                    $sheet->setCellValue("B{$row}", optional($k->dpl)->nama);
                    $row++;

                    $sheet->setCellValue("A{$row}", "No Telp DPL :");
                    $sheet->setCellValue("B{$row}", optional($k->dpl)->no_telp);
                    $row++;

                    $sheet->setCellValue("A{$row}", "APL :");
                    $sheet->setCellValue("B{$row}", optional($k->apl)->nama);
                    $row++;

                    $sheet->setCellValue("A{$row}", "No Telp APL :");
                    $sheet->setCellValue("B{$row}", optional($k->apl)->no_telp);
                    $row++;

                    $sheet->setCellValue("A{$row}", "Kecamatan :");
                    $sheet->setCellValue("B{$row}", $k->nama_kecamatan);
                    $row++;

                    $sheet->setCellValue("A{$row}", "Desa :");
                    $sheet->setCellValue("B{$row}", $k->desa);
                    $row++;

                    $sheet->setCellValue("A{$row}", "Dusun :");
                    $sheet->setCellValue("B{$row}", $k->dusun);
                    $row++;

                    $endInfoRow = $row - 1;

                    $sheet->getStyle("A{$startInfoRow}:B{$endInfoRow}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                    $headerRow = $row;

                    $headers = ['No', 'NIM', 'Nama', 'Prodi', 'Gender'];

                    foreach ($headers as $i => $h) {
                        $col = chr(65 + $i);
                        $sheet->setCellValue("{$col}{$row}", $h);
                    }

                    $sheet->getStyle("A{$row}:E{$row}")
                        ->getFill()->setFillType('solid')
                        ->getStartColor()->setARGB('4F81BD');

                    $sheet->getStyle("A{$row}:E{$row}")
                        ->getFont()->getColor()->setARGB('FFFFFF');

                    $sheet->getStyle("A{$row}:E{$row}")
                        ->getFont()->setBold(true);

                    $row++;

                    $startDataRow = $row;

                    foreach ($k->peserta as $i => $p) {
                        $sheet->setCellValue("A{$row}", $i + 1);
                        $sheet->setCellValue("B{$row}", $p->nim);
                        $sheet->setCellValue("C{$row}", $p->nama);
                        $sheet->setCellValue("D{$row}", $p->prodi);
                        $sheet->setCellValue("E{$row}", $p->gender);
                        $row++;
                    }

                    $endDataRow = $row - 1;

                    $sheet->getStyle("A{$headerRow}:E{$endDataRow}")
                        ->getBorders()->getAllBorders()->setBorderStyle('thin');

                    $row += 2;
                }

                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:E{$lastRow}")
                    ->getAlignment()
                    ->setWrapText(true);
            },
        ];
    }
}