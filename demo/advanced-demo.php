<?php

/**
 * FPDF Grid Areas - Advanced Demo
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

// This (very basic) content array could be grabbed from elsewhere
$content = [
    // page no.
    1 => [
        // grid area name
        'area1' => [
            'text' => "Page One Heading",
            'font' => ['Helvetica', 'B', 20],
            'border' => 'B',
            'align' => 'C',
        ],
        'area2' => [
            'text' => $sideContent,
            'font' => ['Helvetica', '', 10],
            'border' => 0,
            'align' => 'L',
        ],
        'area3' => [
            'text' => $mainContent,
            'font' => ['Helvetica', '', 10],
            'border' => 0,
            'align' => 'L',
        ],
        'area4' => [
            'text' => "Page One Footer",
            'font' => ['Helvetica', '', 10],
            'border' => 'T',
            'align' => 'C',
        ]
    ],
    2 => [
        'area1' => [
            'text' => "Page 2 Heading",
            'font' => ['Helvetica', 'B', 20],
            'border' => 1,
            'align' => 'C',
        ],
        'area2' => [
            'text' => "Some more sidebar content.\n\nLorem ipsum blah blah blah.",
            'font' => ['Helvetica', '', 10],
            'border' => 1,
            'align' => 'L',
        ],
        'area3' => [
            'text' => "Same area name ('area3', different page (2), same layout as page 1.",
            'font' => ['Helvetica', '', 10],
            'border' => 1,
            'align' => 'L',
        ],
        'area4' => [
            'text' => "Page 2 Footer",
            'font' => ['Helvetica', '', 10],
            'border' => 1,
            'align' => 'C',
        ],
    ],
    3 => [
        'area1' => [
            'text' => "Image",
            'image' => "demo-img.png",
            'font' => ['Helvetica', 'B', 10],
            'border' => 'B',
            'align' => 'C',
        ],
        'area2' => [
            'text' => "Page Three Heading",
            'font' => ['Helvetica', 'B', 20],
            'border' => 'B',
            'align' => 'L',
        ],
        'area3' => [
            'text' => trim(preg_replace('/\n[^$]+$/i', '', $sideContent)),
            'font' => ['Helvetica', '', 10],
            'border' => 'R',
            'align' => 'L',
        ],
        'area4' => [
            'text' => $mainContent,
            'font' => ['Helvetica', '', 10],
            'border' => 0,
            'align' => 'L',
        ],
        'area5' => [
            'text' => trim(preg_replace('/^[^\n]+/i', '', $sideContent)),
            'font' => ['Helvetica', '', 10],
            'border' => 'L',
            'align' => 'L',
        ],
        'area6' => [
            'text' => "Page Three Footer",
            'font' => ['Helvetica', '', 10],
            'border' => 'T',
            'align' => 'C',
        ],
    ],
];

// Draw optional borders around page area content.
// We don't add it to Cell() itself, in case the content isn't the full width/height
// of the grid area container.
$drawBorders = function ($borders, $area, $pdf) {
    if (is_string($borders)) {
        $letters = str_split(strtolower($borders));
        sort($letters);
        $borders = implode('', $letters);
    }

    if ($borders === 1 || $borders === 'blrt') {
        $pdf->Rect($area['x'], $area['y'], $area['w'], $area['h']);
    } else {
        if (is_string($borders) && $borders !== '') {
            if (stripos($borders, 't') !== false) {
                $pdf->Line($area['x'], $area['y'], $area['x2'], $area['y']);
            }
            if (stripos($borders, 'r') !== false) {
                $pdf->Line($area['x2'], $area['y'], $area['x2'], $area['y2']);
            }
            if (stripos($borders, 'b') !== false) {
                $pdf->Line($area['x'], $area['y2'], $area['x2'], $area['y2']);
            }
            if (stripos($borders, 'l') !== false) {
                $pdf->Line($area['x'], $area['y'], $area['x'], $area['y2']);
            }
        }
    }
};

$pad = 2; // Amount (mm) to pad grid area content from edges

// Start PDF file
$pdf = new FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(false, 15);

// Show grid lines (set to false, or remove entirely, to hide grid lines)
// The grid lines will only show for the first page using each defined grid.
$pdf->setShowGridLines(true);

$pdf->AddPage(); // You must add a page before you can add a grid.

// Set and define the grid (using percentages here)
$grid1 = $pdf->setGrid(
    ['5%', 0, '3%'],
    ['30%', 0],
    [
        'area1' => [1, 1, 2, 3],
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    ['1%', '3%']
);

// Show the content by looping through the content array for the current page number
foreach ($content[$pdf->PageNo()] as $gridKey => $gridContent) {
    $area = $grid1[$gridKey];

    $pdf->SetXY($area['x'] + $pad, $area['y'] + $pad);

    $pdf->SetFont($gridContent['font'][0], $gridContent['font'][1], $gridContent['font'][2]);

    $drawBorders($gridContent['border'], $area, $pdf);

    if ($gridKey === 'area1' || $gridKey === 'area4') {
        $pdf->Cell($area['w'] - ($pad * 2), $area['h'] - ($pad * 2), $gridContent['text'], 0, 0, $gridContent['align']);
    } else {
        $drawBorders($gridContent['border'], $area, $pdf);
        $pdf->MultiCell($area['w'] - ($pad * 2), 5, $gridContent['text'], 0, $gridContent['align']);
    }
}

$pdf->AddPage();

// Using the previously set grid, the coordinates will always apply to the current page.
// Also remember the helper grid lines will not show this time.
foreach ($content[$pdf->PageNo()] as $gridKey => $gridContent) {
    $area = $grid1[$gridKey];

    $pdf->SetXY($area['x'] + $pad, $area['y'] + $pad);

    $pdf->SetFont($gridContent['font'][0], $gridContent['font'][1], $gridContent['font'][2]);

    $drawBorders($gridContent['border'], $area, $pdf);

    if ($gridKey === 'area1' || $gridKey === 'area4') {
        $pdf->Cell($area['w'] - ($pad * 2), $area['h'] - ($pad * 2), $gridContent['text'], 0, 0, $gridContent['align']);
    } else {
        $pdf->MultiCell($area['w'] - ($pad * 2), 5, $gridContent['text'], 0, $gridContent['align']);
    }
}

$pdf->AddPage('L'); // Change in page orientation

// Reset/redefine the grid when the orientation changes (using user unit this time)
$grid2 = $pdf->setGrid(
    [20, 0, 10],
    [50, 0, 50],
    [   // Using CSS style grid definitions
        'area1' => '1 / 1 / 2 / 2',
        'area2' => '1 / 2 / 2 / 4',
        'area3' => '2 / 1 / 3 / 2',
        'area4' => '2 / 2 / 3 / 3',
        'area5' => '2 / 3 / 3 / 4',
        'area6' => '3 / 1 / 4 / 4',
    ],
    0, // no gaps on this grid
);

foreach ($content[$pdf->PageNo()] as $gridKey => $gridContent) {
    $area = $grid2[$gridKey];

    $pdf->SetXY($area['x'] + $pad, $area['y'] + $pad);

    $pdf->SetFont($gridContent['font'][0], $gridContent['font'][1], $gridContent['font'][2]);

    $drawBorders($gridContent['border'], $area, $pdf);

    if ($gridKey === 'area1') {
        $imgH = $area['h'] - ($pad * 2); // image height, confine to grid area
        $imgW = $imgH * 2; // image width (width:height ratio of image is 2:1)
        $imgXOffset = $area['x'] + (($area['w'] - $imgW) / 2);
        $imgYOffset = $area['y'] + $pad;
        $pdf->Image($gridContent['image'], $imgXOffset, $imgYOffset, $imgW, $imgH);
    } elseif ($gridKey === 'area2' || $gridKey === 'area6') {
        $pdf->Cell($area['w'] - ($pad * 2), $area['h'] - ($pad * 2), $gridContent['text'], 0, 0, $gridContent['align']);
    } else {
        $pdf->MultiCell($area['w'] - ($pad * 2), 5, $gridContent['text'], 0, $gridContent['align']);
    }
}

// Inline output
$pdf->Output();
