<?php

/**
 * FPDF Grid Areas
 * (c) LMD, 2022
 * https://github.com/lmd-code/fpdf-grid-areas
 *
 * @version 0.1.0
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
     * @param array $rows Row sizes in user units or percentage
     * @param array $cols Column sizes in user units or percentage
     * @param array $grid Grid area definitions
     * @param integer $rGap Row gap in user units (optional, default = 0)
     * @param integer $cGap Column gap in user units (optional, default = 0)
     *
     * @return array
     */
    public function grid(array $rows, array $cols, array $grid, float $rGap = 0, float $cGap = 0): array
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
     * @param array $sizes Row sizes in user units (int/float) or percentage (string)
     * @param integer $gap Row gap in user units (default = 0)
     *
     * @return array
     */
    protected function gridRows(array $sizes, float $gap = 0): array
    {
        $numRows = count($sizes);
        $pageHeight = ($this->h - ($this->tMargin + $this->bMargin));
        $gapTotal = ($numRows > 1 && $gap > 0) ? ($numRows - 1) * $gap : 0;

        $sizes = self::percentToFloat($sizes, $pageHeight - $gapTotal);

        $numFlex = count(array_filter($sizes, fn($val) => intval($val) === 0));
        $definedHeight = array_sum($sizes);
        $flexHeight = ($pageHeight - ($definedHeight + $gapTotal)) / $numFlex;

        $rows = [];
        $y = $this->tMargin;
        for ($i = 0; $i < $numRows; $i++) {
            $rowHeight = (intval($sizes[$i]) === 0) ? $flexHeight : $sizes[$i];
            $rows[$i] = ['y' => $y, 'y2' => $y + $rowHeight];
            $y = $y + $rowHeight + $gap;
        }

        return $rows;
    }

    /**
     * Get grid column coordinates
     *
     * @param array $sizes Columns sizes in user units (int/float) or percentage (string)
     * @param integer $gap Column gap in user units (default = 0)
     *
     * @return array
     */
    protected function gridColumns(array $sizes, float $gap = 0): array
    {
        $numCols = count($sizes);
        $pageWidth = ($this->w - ($this->lMargin + $this->rMargin));
        $gapTotal = ($numCols > 1 && $gap > 0) ? ($numCols - 1) * $gap : 0;

        $sizes = self::percentToFloat($sizes, $pageWidth - $gapTotal);

        $numFlex = count(array_filter($sizes, fn($val) => intval($val) === 0));
        $definedWidth = array_sum($sizes);
        $flexWidth = ($pageWidth - ($definedWidth + $gapTotal)) / $numFlex;

        $cols = [];
        $x = $this->lMargin;
        for ($i = 0; $i < $numCols; $i++) {
            $rowWidth = (intval($sizes[$i]) === 0) ? $flexWidth : $sizes[$i];
            $cols[$i] = ['x' => $x, 'x2' => $x + $rowWidth];
            $x = $x + $rowWidth + $gap;
        }

        return $cols;
    }

    /**
     * Convert percentages to floats
     *
     * @param array $vals Values to convert
     * @param float $total Total percentage is proportional to
     *
     * @return array
     */
    private static function percentToFloat(array $vals, float $total): array
    {
        $_vals = [];
        foreach ($vals as $key => $val) {
            $v = floatval($val); // get as float
            if ($v < 0) {
                throw new \InvalidArgumentException(__METHOD__ . ': Value must not be less than 0');
            }
            if (is_string($val) && strpos($val, '%') !== false && $v > 0) {
                $_vals[$key] = $total / (100 / $v);
            } else {
                $_vals[$key] = $v; // already a float, do not convert
            }
        }

        return $_vals;
    }
}
