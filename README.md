# FPDF Grid Areas

If you are familiar with CSS Grid, then you are familiar with FPDF Grid Areas (FPDFGA). Both allow the developer to define areas of a page in a grid system which allows for both fixed and flexible dimensions.

FPDFGA is an extension to the PDF file generator [FPDF](http://www.fpdf.org/) (starting from v1.85).

## Defining a Grid

There is actually more than one way to do this in CSS, but FPDFGA borrows syntax from the `grid-areas` declaration.

### The Grid

In a 3 (rows) x 2 (columns) grid, there are 4 horizontal (rows) and 3 vertical (columns) axis.

```text
         ROW
     1    2    3
   1 +----+----+
     |    |    |
C  2 +----+----+
O    |    |    |
L  3 +----+----+
     |    |    |
   4 +----+----+
```

### CSS

```CSS
#grid {
    
}
#head {
    
}
#main {
    
}
#menu {
    
}
#foot {
    
}
```

### FPDFGA

In FPDFGA flexible widths (`fr`) area indicated with a `0` integer.

```php
$grid = $pdf->grid(
    [20, 0, 10], // rows (3)
    [0, 50], // columns (2)
    [
        'head' => [1, 1, 2, 3], // row start, column start, row end, column end
        'main' => [2, 1, 3, 2],
        'menu' => [2, 2, 3, 3],
        'foot' => [3, 1, 4, 3],
    ], // named grid cells
    5, // row gap (optional)
    5 // column gap (optional)
);
```