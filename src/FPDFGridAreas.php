<?php

/**
 * FPDF Grid Areas
 * (c) LMD, 2022
 * https://github.com/lmd-code/fpdf-grid-areas
 *
 * @version 0.2.0
 */

declare(strict_types=1);

namespace lmdcode\fpdfgridareas;

/**
 * FPDF Grid Areas
 *
 * An extension to [FPDF](http://www.fpdf.org/).
 *
 * Define areas of a PDF page in a grid system using both fixed and flexible dimensions.
 */
class FPDFGridAreas extends \FPDF
{
    /**
     * Calculate and return grid areas
     *
     * @param mixed[] $rows Row sizes in user units (int/float) or percentage (string)
     * @param mixed[] $cols Column sizes in user units (int/float) or percentage (string)
     * @param array<string, int[]> $grid Grid area definitions
     * @param mixed $rGap Row gap in user units (int/float) or percentage (string)
     * @param mixed $cGap Column gap in user units (int/float) or percentage (string)
     *
     * @return array
     */
    public function grid(array $rows, array $cols, array $grid, mixed $rGap = 0, mixed $cGap = 0): array
    {
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

        return $gridItems;
    }

    /**
     * Get grid row coordinates
     *
     * @param mixed[] $sizes Row sizes in user units (int/float) or percentage (string)
     * @param float|int|string $gap Row gap in user units (int/float) or percentage (string)
     *
     * @return float[]
     */
    protected function gridRows(array $sizes, float|int|string $gap = 0): array
    {
        $numRows = count($sizes);
        $pageHeight = ($this->h - ($this->tMargin + $this->bMargin));

        $gap = self::percentToFloat($gap, $pageHeight);

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
     * @param float|int|string $gap Column gap in user units (int/float) or percentage (string)
     *
     * @return float[]
     */
    protected function gridColumns(array $sizes, float|int|string $gap = 0): array
    {
        $numCols = count($sizes);
        $pageWidth = ($this->w - ($this->lMargin + $this->rMargin));

        $gap = self::percentToFloat($gap, $pageWidth);

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
}
