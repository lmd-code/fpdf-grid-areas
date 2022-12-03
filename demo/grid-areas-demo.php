<?php

/**
 * FPDF Grid Areas Basic Demo
 * (c) LMD, 2022
 * https://github.com/lmd-code/fpdf-grid-areas
 */

declare(strict_types=1);

require '../vendor/fpdf/fpdf.php'; // Change this to the location of your copy of FPDF
require '../src/FPDFGridAreas.php';

use lmdcode\fpdfgridareas\FPDFGridAreas;

$pdf = new FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(false, 10);
$pdf->SetFont('Helvetica', '', 12);

$pdf->AddPage();

// User user units (mm)
$grid = $pdf->grid(
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
foreach ($grid as $key => $item) {
    $pdf->Rect($item['x'], $item['y'], $item['w'], $item['h']);
    $pdf->SetXY($item['x'], $item['y']);
    $pdf->Write(5, $key);
}

$pdf->AddPage('L'); // Change in orientation

// Using percentages
$grid = $pdf->grid(
    ['10%', 0, '10%'],
    ['30%', 0],
    [
        'area5' => [1, 1, 2, 3],
        'area6' => [2, 1, 3, 2],
        'area7' => [2, 2, 3, 3],
        'area8' => [3, 1, 4, 3],
    ],
    5,
    5
);

foreach ($grid as $key => $item) {
    $pdf->Rect($item['x'], $item['y'], $item['w'], $item['h']);
    $pdf->SetXY($item['x'], $item['y']);
    $pdf->Write(5, $key);
}

$pdf->Output();
