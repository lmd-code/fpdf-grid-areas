# FPDF Grid Areas

If you are familiar with CSS Grid, then you are familiar with FPDF Grid Areas. Both allow the developer to define areas of a page in a grid system using both fixed and flexible dimensions.

The `FPDFGridAreas` class is an extension to the PDF file generator [FPDF](http://www.fpdf.org/) - you will need to download the `FPDF` class.

## Methods

### `FPDFFridAreas()`  Constructor

*See FPDF class constructor method.*

### `grid($rows, $cols, $grid, $rGap = 0, $cGap = 0)`

| Param | Type | Description |
| ----- | ---- | ------------|
| `$rows` | *array* | Row sizes in user units or percentage |
| `$cols` | *array* | Column sizes in user units or percentage |
| `$grid` | *array* | Grid area definitions |
| `$rGap` | *integer* | Row gap in user units (optional, default = `0`) |
| `$cGap` | *integer* | Column gap in user units (optional, default = `0`) |

#### Row/Column Lengths

Row and column lengths can given in:

- User units as a float or integer value.
- Percentages of the page width/height as string (eg, '25%') - **note:** calculated minus margins and row/column gaps.
- Flexible lengths (`fr` - *fraction units*) can be indicated with a `0` (zero) value.

Each item in the `$rows` or `$cols` array is a new row/column.

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

For example an 1 row high and 2 columns wide at the very top, would be defined in the order "row start / column start / row end / column end" as `1 / 1 / 2 / 3` (or rather as an array: `[1, 1, 2, 3]`).

#### Examples

```php
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
    5,
    5
);
```

See the `demo` folder for a working demo.
