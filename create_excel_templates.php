<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// إنشاء قالب المستخدمين
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// تعيين العناوين
$headers = [
    'First Name',
    'Last Name',
    'Email',
    'Phone',
    'Role',
    'Password',
    'Address',
    'State',
    'Zip Code',
    'Country',
    'City'
];

// تنسيق العناوين
$headerStyle = [
    'font' => [
        'bold' => true,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'E2EFDA',
        ],
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

// إضافة العناوين
foreach ($headers as $colNum => $header) {
    $column = chr(65 + $colNum);
    $sheet->setCellValue($column . '1', $header);
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// تطبيق التنسيق على العناوين
$sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

// إضافة بيانات نموذجية
$sampleData = [
    ['John', 'Doe', 'john.doe@example.com', '+1234567890', 'admin', 'password123', '123 Main St', 'California', '90210', 'USA', 'Los Angeles'],
    ['Jane', 'Smith', 'jane.smith@example.com', '+1987654321', ' admin', 'password456', '456 Oak Ave', 'New York', '10001', 'USA', 'New York']
];

$row = 2;
foreach ($sampleData as $data) {
    $sheet->fromArray($data, null, 'A' . $row);
    $row++;
}

// حفظ ملف المستخدمين
$writer = new Xlsx($spreadsheet);
$writer->save('public/templates/users-import-template.xlsx');

// إنشاء قالب العملاء
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// تعيين العناوين للعملاء
$customerHeaders = [
    'First Name',
    'Last Name',
    'Email',
    'Phone',
    'Password',
    'Address',
    'State',
    'Zip Code',
    'Country',
    'City'
];

// إضافة العناوين للعملاء
foreach ($customerHeaders as $colNum => $header) {
    $column = chr(65 + $colNum);
    $sheet->setCellValue($column . '1', $header);
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// تطبيق التنسيق على العناوين
$sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

// إضافة بيانات نموذجية للعملاء
$customerSampleData = [
    ['John', 'Doe', 'john.doe@example.com', '+1234567890', 'password123', '123 Main St', 'California', '90210', 'USA', 'Los Angeles'],
    ['Jane', 'Smith', 'jane.smith@example.com', '+1987654321', 'password456', '456 Oak Ave', 'New York', '10001', 'USA', 'New York']
];

$row = 2;
foreach ($customerSampleData as $data) {
    $sheet->fromArray($data, null, 'A' . $row);
    $row++;
}

// حفظ ملف العملاء
$writer = new Xlsx($spreadsheet);
$writer->save('public/templates/customers-import-template.xlsx');

echo "تم إنشاء قوالب الإكسل بنجاح!\n";