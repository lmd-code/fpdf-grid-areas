# FPDF Grid Areas

If you are familiar with CSS Grid, then you are familiar with FPDF Grid Areas. They allow the developer to define areas of a page in a grid system using both fixed and flexible dimensions.

The `FPDFGridAreas` class is an extension to the PDF generator class [FPDF](http://www.fpdf.org/) - you will need to download the `FPDF` class.

## Methods

### `new FPDFFridAreas()`

Constructor method.

*See FPDF class constructor method.*

### `grid($rows, $cols, $grid, $rGap = 0, $cGap = 0)`

| Parameter | Type | Default | Description |
| ----- | :--------: | :---: | ------------|
| `$rows` | *array* | | Can specified as a float or integer representing user units, or as a string representing a percentage (see [Percentage Lengths](#percentage-lengths) below). Flexible lengths (`fr` - *fraction units* in CSS) can be indicated with a `0` (zero) value.  Each item in the `$rows` array is a new row. |
| `$cols` | *array* | | Same as above, but for columns. |
| `$grid` | *array* | | Grid area definitions (see [Defining a Grid](#defining-a-grid) below) |
| `$rGap` | *mixed* | `0` | Can specified as a float or integer representing user units, or as a string representing a percentage (see [Percentage Lengths](#percentage-lengths) below). A value of `0` (zero) indicates no gap. |
| `$cGap` | *mixed* | `0` | Same as above, but for columns. |

#### Percentage Lengths

Must be a string, e.g. `'25%'`.

`$rows`/`$cols` - row/column lengths are calculated on the height/width of the page minus both the appropriate margins and the total of all row/column gaps (if row/column gaps are also a percentage, then these are calculated first).

`$rGap`/`$cGsap` - row/column gaps are calculated on the height/width of the page minus the appropriate margins.

#### Defining a Grid

FPDFGA borrows syntax from the CSS grid `grid-area` declaration.

As an example, in a 3 (row) x 2 (column) grid, there are 4 horizontal and 3 vertical axis which are numbered 1 to 3/4 respectively. When defining a grid area the row/column start/end positions refer to the axis.

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

For example an area 1 row high and 2 columns wide at the very top, would be defined in the order "row start / column start / row end / column end" as `1 / 1 / 2 / 3` (or rather as an array: `[1, 1, 2, 3]`).

## Example

```php
$pdf = new \lmdcode\fpdfgridareas\FPDFGridAreas('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(false, 10);

// In user units (mm for example)
$pdf->grid(
    [20, 0, 10], // grid-template-rows: 100px 1fr 50px;
    [0, 50], // grid-template-columns: 1fr 200px;
    [ // named grid areas
        'area1' => [1, 1, 2, 3], // grid-area: 1 / 1 / 2 / 3;
        'area2' => [2, 1, 3, 2], // ditto
        'area3' => [2, 2, 3, 3], // ditto
        'area4' => [3, 1, 4, 3], // ditto
    ],
    5, // grid-row-gap: 5px;
    5 // grid-column-gap: 5px;
);

// As percentages
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
