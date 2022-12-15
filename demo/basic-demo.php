<?php

/**
 * FPDF Grid Areas - Basic Demo
 * (c) LMD, 2022
 * https://github.com/lmd-code/fpdf-grid-areas
 */

declare(strict_types=1);

use lmdcode\fpdfgridareas\FPDFGridAreas;

require '../vendor/fpdf/fpdf.php'; // Change this to the location of your copy of FPDF
require '../src/FPDFGridAreas.php';

// Grab some example content
$content = explode('----split----', file_get_contents('demo-txt.txt'));
$mainContent = trim($content[0]);
$sideContent = trim($content[1]);

// Start PDF file
$pdf = new FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(false, 15); // no auto page break, but set a bottom margin

// Show grid lines (set to false, or remove entirely, to hide grid lines)
// Will only show grid lines on the first page using a particular grid axis
$pdf->setShowGridLines(true);

$pdf->AddPage();

// Define the grid axis and grid areas (using user units in this example).
// If grid lines ar eenable, you must add a page before defining a grid.
$grid = $pdf->setGrid(
    [15, 0, 10], // rows
    [0, 50], // cols
    [
        'head' => [1, 1, 2, 3],
        'main' => [2, 1, 3, 2],
        'side' => [2, 2, 3, 3],
        'foot' => [3, 1, 4, 3],
    ],
    [5, 10] // row, col gaps
);

$lineHeight = 5.25; // default line height in MultiCell (for main/side content)

// Add content

// 'head'
$pdf->SetXY($grid['head']['x'], $grid['head']['y']); // set start position

$pdf->SetFont('Helvetica', 'B', 20); // large title font
$pdf->Cell($grid['head']['w'], $grid['head']['h'], "This is the 'head' area!", 'B', 0, 'C');

// `main`
$pdf->SetXY($grid['main']['x'], $grid['main']['y']); // set start position

$pdf->SetFont('Helvetica', 'B', 12); // bold font (size 12)
$pdf->MultiCell($grid['side']['w'], $lineHeight, "The 'main' area", 0, 'L');

$pdf->Ln($lineHeight); // add blank line

$pdf->SetX($grid['main']['x']); // reset x position

$pdf->SetFont('Helvetica', ''); // regular font
$pdf->MultiCell($grid['main']['w'], $lineHeight, $mainContent, 0, 'J');

// 'side'
$pdf->SetXY($grid['side']['x'], $grid['side']['y']); // set start position

$pdf->SetFont('Helvetica', 'B'); // bold font
$pdf->MultiCell($grid['side']['w'], $lineHeight, "This is the 'side' area", 0, 'L');

$pdf->Ln($lineHeight); // add blank line

$pdf->SetX($grid['side']['x']); // reset x position

$pdf->SetFont('Helvetica', ''); // regular font
$pdf->MultiCell($grid['side']['w'], $lineHeight, $sideContent, 0, 'L');

// 'foot'
$pdf->SetXY($grid['foot']['x'], $grid['foot']['y']); // set start position

$pdf->SetFontSize(10); // small footer font
$pdf->Cell($grid['foot']['w'], $grid['foot']['h'], "This is the 'foot' area", 'T', 0, 'C');

// Inline output
$pdf->Output();
