# FPDF Grid Areas

If you are familiar with CSS Grid, then you are familiar with FPDF Grid Areas. They allow the developer to define areas of a page in a grid system using both fixed and flexible dimensions.

The `FPDFGridAreas` class is an extension to the [FPDF Library](http://www.fpdf.org/) PDF generator, which you will also need to download (it's free, in all senses of the word).

## Methods

In descriptions below "user units" refers to the `$unit` specified in the constructor method (e.g, "mm").

### `new FPDFGridAreas([string $orientation[, string $unit[, mixed $size]]])`

Constructor method.

*See FPDF Library constructor method.*

### `grid(array $rows, array $cols, array $grid[, mixed $rGap[, mixed $cGap]])`

Define a new grid. Will always be based on the current page dimensions - if you change the page size, redefine your grid.

**Returns:** array of grid areas.

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

#### Parameters

##### `$rows` / `$cols`

Row/column sizes. An array of values specified as:-

- Floats or integers representing user units.
- Strings representing a percentage (e.g. `'25%'`).
    - Calculated on the height/width of the page minus both the appropriate margins and the total of all row/column gaps (if row/column gaps are also a percentage, then these are calculated first).
- A value of `0` (zero) indicating a flexible length (*fraction units*, `fr`, in CSS) to be automatically calculated from any remaing space.

##### `$grid`

Grid area definition. An array of named areas, where each area (array key) is specified by an array of integers representing the edges of each area in the order: row start, column start, row end, column end.

```text
[
    'area name' => [row start, column start, row end, column end],
]
```

The syntax is borrowed from CSS grid (specifically the `grid-area` property[^1]), and therefore uses the same axis numbering system. For example, in a 3 (row) by 2 (column) grid there are 4 horizontal and 3 vertical axis numbered 1-3 and 1-4 respectively. When defining a grid area the row/column start/end positions refer to the axis.

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

This means that an area that is 1 row high and 2 columns wide placed at the top of the grid would be defined by the array `'name' => [1, 1, 2, 3]` (see [Examples](#examples)).

##### `$rGap` / `$cGap`

Row/column gaps. Values specified as:-

- Floats or integers representing user units.
- Strings representing a percentage (e.g. `'25%'`).
    - Calculated on the height/width of the page minus the appropriate margins.
- A value of `0` (zero) indicating that there should be no gap.
 
**Default:** `0`.

## Examples

### Using User Units

```php
// Setup page
$pdf = new \lmdcode\fpdfgridareas\FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(false, 10);

// Define grid
$pdf->grid(
    [20, 0, 10], // grid-template-rows: 20mm 1fr 10mm;
    [0, 50], // grid-template-columns: 1fr 50mm;
    [ // named grid areas
        'area1' => [1, 1, 2, 3], // grid-area: 1 / 1 / 2 / 3;
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    5, // grid-row-gap: 5mm;
    5 // grid-column-gap: 5mm;
);
```

### Using Percentages

```php
$pdf->grid(
    ['10%', 0, '5%'],
    [0, '25%'],
    [
        'area1' => [1, 1, 2, 3],
        'area2' => [2, 1, 3, 2],
        'area3' => [2, 2, 3, 3],
        'area4' => [3, 1, 4, 3],
    ],
    '1%',
    '1%'
);
```

See the `demo` folder for a working demo.

[^1] https://developer.mozilla.org/en-US/docs/Web/CSS/grid-area
