<?php

/**
 * FPDF Grid Areas Basic Demo
 * (c) LMD, 2022
 * https://github.com/lmd-code/fpdf-grid-areas
 */

declare(strict_types=1);

use lmdcode\fpdfgridareas\FPDFGridAreas;

require '../vendor/fpdf/fpdf.php'; // Change this to the location of your copy of FPDF
require '../src/FPDFGridAreas.php';

// This (very basic) content array could be stored elsewhere, obviously.
$content = [
    // page no.
    1 => [
        // grid area name
        'area2' => "This is the content for this page!"
    ],
    2 => [
        'area2' => "Same area name, different page, different layout."
    ],
    3 => [
        'area2' => "Same area name, different page, same layout as first page."
    ],
];

$pdf = new FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(false, 10);
$pdf->SetFont('Helvetica', '', 12);

$pdf->AddPage();

// User user units (mm)
$grid1 = $pdf->grid(
    [20, 0, 10],
    [0, 50],
    [
        'area1' => [1, 1, 2, 3],
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    5,
    5
);

// To show the areas, we'll draw some rectangles and add text labels
foreach ($grid1 as $key => $item) {
    $pdf->Rect($item['x'], $item['y'], $item['w'], $item['h']);
    $pdf->SetXY($item['x'], $item['y']);
    $pdf->Write(5, $key);
    if (!empty($content[$pdf->PageNo()][$key])) {
        $pdf->SetXY($item['x'] + 2.5, $item['y'] + 7.5);
        $pdf->MultiCell(
            $item['w'] - 5,
            5,
            $content[$pdf->PageNo()][$key], // use the page number to get the right content
            0,
            'L'
        );
    }
}

$pdf->AddPage('L'); // Change in orientation

// Using percentages
$grid2 = $pdf->grid(
    ['10%', 0, '10%'],
    ['30%', 0],
    [
        'area1' => [1, 1, 2, 3],
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    '1%',
    '1%'
);

foreach ($grid2 as $key => $item) {
    $pdf->Rect($item['x'], $item['y'], $item['w'], $item['h']);
    $pdf->SetXY($item['x'], $item['y']);
    $pdf->Write(5, $key);
    if (!empty($content[$pdf->PageNo()][$key])) {
        $pdf->SetXY($item['x'] + 2.5, $item['y'] + 7.5);
        $pdf->MultiCell(
            $item['w'] - 5,
            5,
            $content[$pdf->PageNo()][$key],
            0,
            'L'
        );
    }
}

$pdf->AddPage(); // Change back orientation

// This time we are going to use our first grid again, but we dont' need to redeclare it.
// The coordinates will always apply to the current page.
foreach ($grid1 as $key => $item) {
    $pdf->Rect($item['x'], $item['y'], $item['w'], $item['h']);
    $pdf->SetXY($item['x'], $item['y']);
    $pdf->Write(5, $key);
    if (!empty($content[$pdf->PageNo()][$key])) {
        $pdf->SetXY($item['x'] + 2.5, $item['y'] + 7.5);
        $pdf->MultiCell(
            $item['w'] - 5,
            5,
            $content[$pdf->PageNo()][$key],
            0,
            'L'
        );
    }
}

// We can directly reference a grid area without having to loop through it.
$pdf->SetXY($grid1['area4']['x'], $grid1['area4']['y']);
$pdf->Cell(
    $grid1['area4']['w'],
    $grid1['area4']['h'],
    "This is a directly referenced grid area!",
    0,
    0,
    'C'
);

$pdf->Output();
