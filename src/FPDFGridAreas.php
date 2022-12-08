<?php

/**
 * FPDF Grid Areas
 * (c) LMD, 2022
 * https://github.com/lmd-code/fpdf-grid-areas
 *
 * @version 1.0.0
 */

declare(strict_types=1);

namespace lmdcode\fpdfgridareas;

/**
 * FPDF Grid Areas
 *
 * An extension to [FPDF Library](http://www.fpdf.org/).
 *
 * Define areas of a PDF page in a grid system using both fixed and flexible dimensions.
 */
class FPDFGridAreas extends \FPDF
{
    /**
     * Show grid lines flag
     * @var boolean
     */
    private $showGridLines = false;

    /**
     * Used alpha transparencies
     * @var array
     */
    private $alphaTransparency = [];

    /**
     * Show grid lines during development.
     *
     * Grid lines will only show on the first page using each defined grid.
     *
     * @param boolean $flag Show grid lines (true = yes, false = no)
     *
     * @return void
     */
    public function setShowGridLines(bool $flag): void
    {
        $this->showGridLines = $flag;
    }

    /**
     * Calculate and return grid areas
     *
     * @param mixed[] $rows Row sizes in user units (int/float) or percentage (string)
     * @param mixed[] $cols Column sizes in user units (int/float) or percentage (string)
     * @param array<string, int[]> $grid Array of grid area definitions in the format:
     *                                   `'name' => 'row start, col start, row end, col end',`
     *                                   E.g., `'area1' => [1, 1, 2, 3],`
     * @param mixed $gap Row/Column gap in user units (int/float) or percentage (string).
     *                   - Single value (int/float/atring) for equal row/column gaps (`5`)
     *                   - An array of values (as above) for different row/column gaps (`[5, 10]`)
     *
     * @return array
     */
    public function setGrid(array $rows, array $cols, array $grid, mixed $gap = 0): array
    {
        $pageHeight = ($this->h - ($this->tMargin + $this->bMargin));
        $pageWidth = ($this->w - ($this->lMargin + $this->rMargin));

        if (is_array($gap)) {
            $rGap = self::percentToFloat(isset($gap[0]) ? $gap[0] : 0, $pageHeight);
            $cGap = self::percentToFloat(isset($gap[1]) ? $gap[1] : 0, $pageWidth);
        } else {
            $rGap = self::percentToFloat($gap, $pageHeight);
            $cGap = self::percentToFloat($gap, $pageWidth);
        }

        $gridRows = $this->gridRows($rows, $rGap);
        $gridCols = $this->gridColumns($cols, $cGap);

        $gridItems = [];

        foreach ($grid as $key => $val) {
            $r1 = $val[0] - 1;
            $c1 = $val[1] - 1;
            $r2 = $val[2] - 1;
            $c2 = $val[3] - 1;

            $rSpan = $r2 - $r1;
            $cSpan = $c2 - $c1;

            $yBeg = $gridRows[$r1]['y'];
            $yEnd = $gridRows[($r1 + $rSpan) - 1]['y2'];
            $xBeg = $gridCols[$c1]['x'];
            $xEnd = $gridCols[($c1 + $cSpan) - 1]['x2'];
            $w = $xEnd - $xBeg;
            $h = $yEnd - $yBeg;

            $gridItems[$key] = [
                'x' => $xBeg,
                'y' => $yBeg,
                'x2' => $xEnd,
                'y2' => $yEnd,
                'w' => $w,
                'h' => $h,
            ];
        }

        if ($this->showGridLines) {
            $this->drawGridLines($gridRows, $gridCols, $rGap, $cGap, $gridItems);
        }

        return $gridItems;
    }

    /**
     * Get grid row coordinates
     *
     * @param mixed[] $sizes Row sizes in user units (int/float) or percentage (string)
     * @param float|int|string $gap Row gap in user units
     *
     * @return float[]
     */
    protected function gridRows(array $sizes, float $gap = 0): array
    {
        $numRows = count($sizes);
        $pageHeight = ($this->h - ($this->tMargin + $this->bMargin));

        $gapTotal = ($numRows > 1) ? ($numRows - 1) * $gap : 0;

        $contentHeight = $pageHeight - $gapTotal;
        for ($i = 0; $i < $numRows; $i++) {
            $sizes[$i] = self::percentToFloat($sizes[$i], $contentHeight);
        }

        $numFlex = count(array_filter($sizes, fn($val) => intval($val) === 0));
        $definedHeight = array_sum($sizes);
        $flexHeight = ($pageHeight - ($definedHeight + $gapTotal)) / $numFlex;

        $rows = [];
        $y = $this->tMargin;
        for ($i = 0; $i < $numRows; $i++) {
            // can't compare floats accurately, so compare strings
            $rowHeight = (strval($sizes[$i]) === '0') ? $flexHeight : $sizes[$i];
            $rows[$i] = ['y' => $y, 'y2' => $y + $rowHeight];
            $y = $y + $rowHeight + $gap;
        }

        return $rows;
    }

    /**
     * Get grid column coordinates
     *
     * @param mixed[] $sizes Columns sizes in user units (int/float) or percentage (string)
     * @param float $gap Column gap in user units
     *
     * @return float[]
     */
    protected function gridColumns(array $sizes, float $gap = 0): array
    {
        $numCols = count($sizes);
        $pageWidth = ($this->w - ($this->lMargin + $this->rMargin));

        $gapTotal = ($numCols > 1) ? ($numCols - 1) * $gap : 0;

        $contentWidth = $pageWidth - $gapTotal;
        for ($i = 0; $i < $numCols; $i++) {
            $sizes[$i] = self::percentToFloat($sizes[$i], $contentWidth);
        }

        $numFlex = count(array_filter($sizes, fn($val) => intval($val) === 0));
        $definedWidth = array_sum($sizes);
        $flexWidth = ($pageWidth - ($definedWidth + $gapTotal)) / $numFlex;

        $cols = [];
        $x = $this->lMargin;
        for ($i = 0; $i < $numCols; $i++) {
            $rowWidth = (strval($sizes[$i]) === '0') ? $flexWidth : $sizes[$i];
            $cols[$i] = ['x' => $x, 'x2' => $x + $rowWidth];
            $x = $x + $rowWidth + $gap;
        }

        return $cols;
    }

    /**
     * Draw grid lines if flag is set to true
     *
     * @param array $rows Row coordinates
     * @param array $cols Column coordinates
     * @param float $rGap Row gap
     * @param float $cGap Column gap
     * @param array $grid Grid area definitions (optional)
     *
     * @return void
     */
    protected function drawGridLines(array $rows, array $cols, float $rGap, float $cGap, array $grid = []): void
    {
        // Store current settings
        $current = [
            'fontFamily' => $this->FontFamily,
            'fontStyle' => $this->FontStyle,
            'fontSize' => $this->FontSizePt,
            'textColor' => $this->TextColor,
            'lineWidth' => $this->LineWidth,
            'drawColor' => $this->DrawColor,
            'fillColor' => $this->FillColor,
            'cMargin' => $this->cMargin,
        ];

        $fontSize = 10; // for axis line numbers

        // Set temporary settings
        $this->SetFont('Courier', 'B', $fontSize);
        $this->SetTextColor(100, 100, 100);
        $this->SetDrawColor(255, 0, 0);
        $this->SetLineWidth(0.1);
        $this->cMargin = 0;

        $numRows = count($rows);
        $numCols = count($cols);

        // Font sizes - fixed width font, all chars the same
        $fontHeight = $this->FontSize; // font size in user unit
        $fontWidth = ($this->CurrentFont['cw']['0'] * ($fontHeight / 1000)) * strlen('' . $numRows);

        $fontYOffset = $fontHeight / 2; // vertial centre of text
        $fontXOffset = $fontWidth / 2; // horizontal centre of text
        $edgeOffset = 1; // offset text from edge of page (mm)

        $rGapOffset = ($rGap > 0) ? $rGap / 2 : 0; // centre of row gap
        $cGapOffset = ($cGap > 0) ? $cGap / 2 : 0; // centre of column gap

        // Draw row/col gap rectangles
        $this->SetFillColor(208, 208, 208);
        foreach ($rows as $k => $row) {
            if ($k > 0) {
                $this->Rect(
                    $this->lMargin,
                    $row['y'] - $rGap,
                    $this->w - ($this->lMargin + $this->rMargin),
                    $rGap,
                    'F'
                );
            }
        }
        foreach ($cols as $k => $col) {
            if ($k > 0) {
                $this->Rect(
                    $col['x'] - $cGap,
                    $this->tMargin,
                    $cGap,
                    $this->h - ($this->tMargin + $this->bMargin),
                    'F'
                );
            }
        }

        // Draw lines and labels
        $this->SetFillColor(255, 255, 255);

        // Draw rows
        foreach ($rows as $k => $row) {
            $yPos = $row['y'] - ($k > 0 ? $rGapOffset : 0); // no offset on top edge line

            $this->Line(0, $yPos, $this->w, $yPos);

            // Left edge
            $this->SetXY($edgeOffset, $yPos - $fontYOffset);
            $this->Cell($fontWidth, $fontHeight, $k + 1, 0, 0, 'C', true);

            // Right edge
            $this->SetXY($this->w - ($fontWidth + $edgeOffset), $yPos - $fontYOffset);
            $this->Cell($fontWidth, $fontHeight, $k + 1, 0, 0, 'C', true);

            // Bottom line on last row
            if (($k + 1) === $numRows) {
                $this->Line(0, $row['y2'], $this->w, $row['y2']);

                // Left edge
                $this->SetXY($edgeOffset, $row['y2'] - $fontYOffset);
                $this->Cell($fontWidth, $fontHeight, $k + 2, 0, 0, 'C', true);

                // Right edge
                $this->SetXY($this->w - ($fontWidth + $edgeOffset), $row['y2'] - $fontYOffset);
                $this->Cell($fontWidth, $fontHeight, $k + 2, 0, 0, 'C', true);
            }
        }

        // Draw columns
        foreach ($cols as $k => $col) {
            $xPos = $col['x'] - ($k > 0 ? $cGapOffset : 0); // no offset on left edge line

            $this->Line($xPos, 0, $xPos, $this->h);

            // Top edge
            $this->SetXY($xPos - $fontXOffset, $edgeOffset);
            $this->Cell($fontWidth, $fontHeight, $k + 1, 0, 0, 'C', true);

            // Bottom edge
            $this->SetXY($xPos - $fontXOffset, $this->h - ($fontHeight + $edgeOffset));
            $this->Cell($fontWidth, $fontHeight, $k + 1, 0, 0, 'C', true);

            // Right edge line on last column
            if (($k + 1) === $numCols) {
                $this->Line($col['x2'], 0, $col['x2'], $this->h);

                // Top edge
                $this->SetXY($col['x2'] - $fontXOffset, $edgeOffset);
                $this->Cell($fontWidth, $fontHeight, $k + 2, 0, 0, 'C', true);

                // Bottom edge
                $this->SetXY($col['x2'] - $fontXOffset, $this->h - ($fontHeight + $edgeOffset));
                $this->Cell($fontWidth, $fontHeight, ($k + 2), 0, 0, 'C', true);
            }
        }

        // Grid areas and labels
        $this->SetDrawColor(0, 0, 255);
        $this->SetLineWidth(0.5);
        foreach ($grid as $key => $item) {
            $this->enableAlphaTransparency(true);
            $this->Rect($item['x'], $item['y'], $item['w'], $item['h'], 'FD');
            $this->enableAlphaTransparency(false);
            $this->SetXY($item['x'] + 1, $item['y'] - $fontYOffset);
            $this->Cell($this->GetStringWidth($key), $fontHeight, $key, 0, 0, 'L', true);
        }

        // Reset current settings
        $this->SetFont($current['fontFamily'], $current['fontStyle'], $current['fontSize']);
        $this->SetLineWidth($current['lineWidth']);
        $this->TextColor = $current['textColor'];
        $this->DrawColor = $current['drawColor'];
        $this->FillColor = $current['fillColor'];
        $this->cMargin = $current['cMargin'];
        if ($this->page > 0) {
            $this->_out($current['textColor']); // output current text colour
            $this->_out($current['drawColor']); // output current draw colour
            $this->_out($current['fillColor']); // output current fill colour
        }
        $this->SetXY($this->lMargin, $this->tMargin); // reset coords to content origin
    }

    /**
     * Enable alpha transparency
     *
     * When enabled, applies to whole document until disabled.
     *
     * @param boolean $alpha Enable the alpha transparency (default = `false`).
     *                       - `true` sets fill alpha to 0.5 (50% opacity)
     *                       - `false` restores fill alpha to 1 (100% opacity/opaque)
     *
     * @return void
     */
    protected function enableAlphaTransparency(bool $alpha = false): void
    {
        // ca = non-stroking (fill), CA = stroking (lines/text), BA = blend mode
        if ($alpha) {
            $this->alphaTransparency[1] = ['ca' => 0.5, 'CA' => 1, 'BM' => '/Normal'];
            $this->_out(sprintf('/AlphaTransparency%d gs', 1));
        } else {
            $this->alphaTransparency[2] = ['ca' => 1, 'CA' => 1, 'BM' => '/Normal'];
            $this->_out(sprintf('/AlphaTransparency%d gs', 2));
        }

        $this->PDFVersion = '1.4'; // using alpha transparency
    }

    /**
     * Convert percentage to float
     *
     * @param float|int|string $val Value to convert
     * @param float|int $total Total value percentage is proportional to
     *
     * @return float
     */
    private static function percentToFloat(float|int|string $val, float|int $total): float
    {
        // If float or int, immediately return
        if (is_int($val) || is_float($val)) {
            return floatval($val); // force int to float
        }

        // Is value a vald percentage string (e.g., '50%', '100%' or '33.33%')
        if (preg_match('/^\d{1,3}(\.\d+)?%$/', $val) !== 1) {
            throw new \InvalidArgumentException(__METHOD__ . ': argument must be a valid percentage string. ' . $val);
        }

        $pc = floatval($val); // convert valid string to float

        // Percentage must be between 0-100
        if ($pc < 0 || $pc > 100) {
            throw new \InvalidArgumentException(__METHOD__ . ': argument must be a value from 0 to 100 percent.');
        }

        // Everything is ok, do calculation
        return  floatval(($pc / 100) * $total);
    }

    /**
     * Overwrite FPDF parent resource dictionary method
     * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
     * @return void
     */
    protected function _putresourcedict(): void
    {
        parent::_putresourcedict(); // call parent first

        $this->_put('/ExtGState <<');
        foreach ($this->alphaTransparency as $key => $level) {
            $this->_put('/AlphaTransparency' . $key . ' ' . $level['n'] . ' 0 R');
        }
        $this->_put('>>');
    }

    /**
     * Overwrite FPDF parent resources method
     * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
     * @return void
     */
    protected function _putresources(): void
    {
        foreach ($this->alphaTransparency as $key => $level) {
            $this->_newobj();
            $this->alphaTransparency[$key]['n'] = $this->n; // store object number
            $this->_put('<</Type /ExtGState');
            $this->_put(sprintf('/ca %.3F', $level['ca']));
            $this->_put(sprintf('/CA %.3F', $level['CA']));
            $this->_put('/BM ' . $level['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }

        parent::_putresources(); // now call parent
    }

    /**
     * @deprecated Deprecated, use `setGrid()` instead. Will be removed in future versions.
     */
    public function grid($rows, $cols, $grid, $rGap = 0, $cGap = 0)
    {
        return $this->setGrid($rows, $cols, $grid, [$rGap, $cGap]);
    }
}
