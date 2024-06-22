# FPDF Grid Areas

If you are familiar with CSS Grid, then you are familiar with FPDF Grid Areas. They allow the developer to define areas of a page in a grid system using both fixed and flexible dimensions.

## Requirements

- **FPDF Library:** The `FPDFGridAreas` class is an extension to the [FPDF Library](http://www.fpdf.org/) PDF generator, which you will also need to download (it's free, in all senses of the word).
- **PHP Version:** Compatible with PHP versions 8.0+

## Methods

In descriptions below "user units" refers to the `$unit` specified in the constructor method (e.g, "mm").

### `new FPDFGridAreas([string $orientation[, string $unit[, mixed $size]]])`

Constructor method.

*See FPDF Library constructor method.*

### `setShowGridLines(bool $flag)`

Show grid lines during development. Grid lines will only show on the first page using each defined grid.

#### Parameters

<dl>
<dt>

`$flag`

</dt>
<dd>

Show grid lines: `true` = yes, `false` = no.

**Default:** `false`.

</dd>
</dl>

### `setGrid(array $rows, array $cols, array $grid[, mixed $gap])`

Define a new grid. Will always be based on the current page dimensions - if you change the page size, redefine your grid.

#### Parameters

<dl>
<dt>

`$rows` / `$cols`

</dt>
<dd>

Row/column sizes. An array of values specified as:-

- Floats or integers representing user units.
- Strings representing a percentage (e.g. `'25%'`).
    - Calculated on the height/width of the page minus both the appropriate margins and the total of all row/column gaps (if row/column gaps are also a percentage, then these are calculated first).
- A value of `0` (zero) indicating a flexible length (*fraction units*, `fr`, in CSS) to be automatically calculated from any remaining space.

</dd>
<dt>

`$grid`

</dt>
<dd>

Grid area definition.

An array of named areas, where each area (array key) is specified by an array of integers representing the edges of each area in the order: row start, column start, row end, column end. See [Defining a Grid](#defining-a-grid) for more detail.

You can pass an empty array (`$grid = []`) in conjunction with `setShowGridLines()` to view the lines numbers (see [Show Grid Lines Helper](#show-grid-lines-helper)).

</dd>
<dt>

`$gap`

</dt>
<dd>

Row/column gaps can be specified as:-

- A single integer, float or string applying the same value to both the row and column gap.
    - Floats or integers represent user units.
    - Strings represent a percentage (e.g. `'25%'`).
    - A value of `0` (zero) indicates that there should be no gap.
- An array of integers, floats or strings (e.g., `[5, 10]`) applies different gap values in the order "row, column".
    - Note: an array with a single value will apply to the row gap only, the column gap will be `0` (zero).

**Remember:** Percentages are calculated on the height/width of the page minus the appropriate margins. When using the same percentage for row and column gaps, the result will not be equal in user unit size when the page height and width (including different margin sizes) are not equal. If you want row/column gaps to be equal, use user units.

**Default:** `0`.

</dd>
</dl>

#### Returns

Returns an array of grid areas with coordinates and dimensions calculated.

```text
[
    'area name' => [
        'x' => float,  // left edge
        'y' => float,  // top edge
        'x2' => float, // right edge
        'y2' => float, // bottom edge
        'w' => float,  // width
        'h' => float,  // height
    ],
]
```

## Defining a Grid

The grid syntax is borrowed from CSS grid (specifically the `grid-area` property[^1]), and therefore uses the same axis numbering system. For example, in a 3 (row) by 2 (column) grid there are 4 horizontal and 3 vertical axis numbered 1-3 and 1-4 respectively. When defining a grid area the row/column start/end positions refer to the axis.

```text
       COLUMNS
     1    2    3
   1 +----+----+
R    |    |    |
O  2 +----+----+
W    |    |    |
S  3 +----+----+
     |    |    |
   4 +----+----+
```

This means that an area that is 1 row high and 2 columns wide placed at the top of the grid would be defined by the array `'areaName' => [1, 1, 2, 3]` (in CSS it's `#areaName { grid-area: 1 / 1 / 2 / 3; }`).

```php
// 'area name' => [row start, column start, row end, column end],
$grid = [
    'area1' => [1, 1, 2, 2],
    'area2' => [2, 1, 3, 2],
    //... etc
]
```

### Show Grid Lines Helper

An easy way to visualise the row/column axis required, is to set `setShowGridLines(true)` and pass an empty array to the `$grid` argument of the `setGrid()` method. You can then work out each area's row/column start/end axis.

```php
$pdf->setShowGridLines(true);
$pdf->setGrid([20, 0, 10], [0, 50], [], 5); // note empty $grid argument
```

## Example

```php
// Setup page
$pdf = new \lmdcode\fpdfgridareas\FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(false, 10);

$pdf->AddPage(); // You must add a page before you can add a grid.

// Define grid using User Units
$pdf->setGrid(
    [20, 0, 10], // grid-template-rows: 20mm 1fr 10mm;
    [0, 50], // grid-template-columns: 1fr 50mm;
    [ // named grid areas
        'area1' => [1, 1, 2, 3], // grid-area: 1 / 1 / 2 / 3;
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    [5, 10], // grid-row-gap: 5mm; /  grid-column-gap: 10mm;
);

$pdf->AddPage(); // New page

// Define a grid using percentages
$pdf->setGrid(
    ['10%', 0, '5%'],
    [0, '25%'],
    [
        'area1' => [1, 1, 2, 3],
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    '1%'
);
```

See the `demo` folder for a working demo.

[^1]: https://developer.mozilla.org/en-US/docs/Web/CSS/grid-area
