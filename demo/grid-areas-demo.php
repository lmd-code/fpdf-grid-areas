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
        'area1' => [
            'text' => "Some Sort of Heading...",
            'font' => ['Helvetica', 'B', 20],
            'align' => 'C',
        ],
        'area2' => [
            'text' => "This is the main content for this page.",
            'font' => ['Helvetica', '', 10],
            'align' => 'L',
        ],
        'area3' => [
            'text' => "- Item A\n- Item B\n- Item C",
            'font' => ['Helvetica', '', 10],
            'align' => 'L',
        ],
    ],
    2 => [
        'area1' => [
            'text' => "Another Heading",
            'font' => ['Helvetica', 'B', 20],
            'align' => 'C',
        ],
        'area2' => [
            'text' => "Same area name, different page, different layout.",
            'font' => ['Helvetica', '', 10],
            'align' => 'L',
        ],
        'area3' => [
            'text' => "No sit lorem aliquyam erat diam, lorem no dolor stet voluptua dolor sed tempor eirmod sed, sadipscing vero lorem sadipscing amet no. Dolor sed tempor clita elitr justo. Lorem et lorem dolores sit sit consetetur sea, nonumy et dolor sea lorem. Accusam duo gubergren voluptua sadipscing dolor amet. Duo erat et gubergren et duo sadipscing. Magna sit diam erat amet sit, est et gubergren diam amet ut sadipscing. Dolor diam invidunt dolor accusam justo eos justo kasd no. Tempor kasd eirmod ut sadipscing eirmod elitr diam. Sadipscing accusam lorem eos dolor, dolores amet accusam amet diam rebum et duo sed.",
            'font' => ['Helvetica', '', 10],
            'align' => 'L',
        ],
        'area4' => [
            'text' => "Page Footer... blah blah blah",
            'font' => ['Helvetica', '', 10],
            'align' => 'C',
        ],
    ],
    3 => [
        'area1' => [
            'text' => "Guess what? A heading!",
            'font' => ['Helvetica', 'B', 20],
            'align' => 'C',
        ],
        'area2' => [
            'text' => "Same area name, different page, same layout as first page.",
            'font' => ['Helvetica', '', 10],
            'align' => 'L',
        ],
        'area3' => [
            'text' => "Some more sidebar content.",
            'font' => ['Helvetica', '', 10],
            'align' => 'L',
        ],
        'area4' => [
            'text' => "A different page footer",
            'font' => ['Helvetica', '', 10],
            'align' => 'C',
        ],
    ],
];

$pdf = new FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(false, 10);

// Show grid lines (set to false, or remove entirely, to hide grid lines)
// The grid lines will only show for the first page using each defined grid.
$pdf->setShowGridLines(true);

$pdf->AddPage(); // You must add a page before you can add a grid.

// User user units (mm)
$grid1 = $pdf->setGrid(
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

// Show the content by looping through the content array for the current page number
foreach ($content[$pdf->PageNo()] as $gridKey => $gridContent) {
    $area = $grid1[$gridKey];

    $pdf->SetXY($area['x'] + 1, $area['y'] + 4);

    $pdf->SetFont($gridContent['font'][0], $gridContent['font'][1], $gridContent['font'][2]);
    $pdf->MultiCell($area['w'] - 2, 5, $gridContent['text'], 0, $gridContent['align']);
}

// We can also directly reference a grid area
$pdf->SetXY($grid1['area4']['x'], $grid1['area4']['y']);
$pdf->Cell(
    $grid1['area4']['w'],
    $grid1['area4']['h'],
    "This is a directly referenced grid area!",
    0,
    0,
    'C'
);

$pdf->AddPage('L'); // Change in orientation

// Using percentages
$grid2 = $pdf->setGrid(
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

foreach ($content[$pdf->PageNo()] as $gridKey => $gridContent) {
    $area = $grid2[$gridKey];

    $pdf->SetXY($area['x'] + 1, $area['y'] + 4);

    $pdf->SetFont($gridContent['font'][0], $gridContent['font'][1], $gridContent['font'][2]);
    $pdf->MultiCell($area['w'] - 2, 5, $gridContent['text'], 0, $gridContent['align']);
}

$pdf->AddPage(); // Change back orientation

// This time we are going to use our first grid again, but we don't need to
// redeclare it, the coordinates will always apply to the current page.
// Also remember, the helper grid lines will not show this time.
foreach ($content[$pdf->PageNo()] as $gridKey => $gridContent) {
    $area = $grid1[$gridKey];

    $pdf->SetXY($area['x'] + 1, $area['y'] + 4);

    $pdf->SetFont($gridContent['font'][0], $gridContent['font'][1], $gridContent['font'][2]);
    $pdf->MultiCell($area['w'] - 2, 5, $gridContent['text'], 0, $gridContent['align']);
}

$pdf->Output();
