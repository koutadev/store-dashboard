<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StoreExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        $query = Store::with('area')->withCount('sales');

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $stores = $query->orderBy('code')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('店舗データ');

        // ヘッダー
        $headers = ['店舗コード', '店舗名', 'エリア', '住所', '電話番号', '売上件数', '状態'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // ヘッダースタイル
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // データ行
        $row = 2;
        foreach ($stores as $store) {
            $sheet->setCellValue("A{$row}", $store->code);
            $sheet->setCellValue("B{$row}", $store->name);
            $sheet->setCellValue("C{$row}", $store->area->name);
            $sheet->setCellValue("D{$row}", $store->address ?? '');
            $sheet->setCellValue("E{$row}", $store->phone ?? '');
            $sheet->setCellValue("F{$row}", $store->sales_count);
            $sheet->setCellValue("G{$row}", $store->is_active ? '営業中' : '閉鎖');
            $row++;
        }

        // 数値フォーマット
        $lastRow = $row - 1;
        $sheet->getStyle("F2:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        // 列幅自動調整
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 罫線
        $sheet->getStyle("A1:G{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // ダウンロード
        $filename = '店舗データ_' . date('Ymd_His');

        if ($format === 'csv') {
            $filename .= '.csv';
            $writer = new Csv($spreadsheet);
            $writer->setUseBOM(true);
            $contentType = 'text/csv';
        } else {
            $filename .= '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'export');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => $contentType,
        ])->deleteFileAfterSend(true);
    }
}