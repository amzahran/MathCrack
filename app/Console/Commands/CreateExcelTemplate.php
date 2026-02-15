<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CreateExcelTemplate extends Command
{
    protected $signature = 'template:create-excel';
    protected $description = 'Create Excel template for customer import';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // تعيين العناوين
        $headers = ['First Name', 'Last Name', 'Email', 'Phone', 'Password'];
        $sheet->fromArray($headers, NULL, 'A1');

        // تنسيق العناوين
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // إضافة أمثلة
        $examples = [
            ['John', 'Doe', 'john@example.com', '+1234567890', 'password123'],
            ['Jane', 'Smith', 'jane@example.com', '+0987654321', 'password456'],
        ];
        $sheet->fromArray($examples, NULL, 'A2');

        // تنسيق الأمثلة
        $exampleStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A2:E3')->applyFromArray($exampleStyle);

        // ضبط عرض الأعمدة
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // حفظ الملف
        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path('templates/customers-import-template.xlsx'));

        $this->info('Excel template created successfully!');
    }
}