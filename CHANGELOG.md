# Changelog

## [v1.1.0] - 2024-09-02

### Changed

- Change `$grid` array argument in `setGrid()` to allow use of CSS grid syntax string or an array of integers for grid area definitions.
- Update README to document `setGrid()` argument change.
- Update demo code with examples of CSS grid syntax.

## [v1.0.1] - 2022-12-15

### Changed:

- Updated/rewrote demo examples.

### Fixed

- Bugfix: Line widths set by `drawGridLines()` are now correctly sized when the user set unit is not 'mm'.
- Bugfix: Decimal (float) grid row/column values less than `1` (e.g. `0.5`), no longer incorrectly treated as a flexible row/column.

## [v1.0.0] - 2022-12-08

First major release.

### Changed

- Change `grid()` method name to `setGrid()`
- Deprecate `grid()` (will be removed in future version).
- Merge row/column gap args (`$rGap`/`$cGap`) into single `$gap` arg.
- When show grid lines enabled, outline and fill grid areas with 50% transparency and add label.
- Update README
- Update demo code.

## [v0.3.2] - 2022-12-05

### Fixed

- Fix bug where `$rGap` and `$cGap` in `grid()` weren't being converted to floats soon enough.

## [v0.3.1] - 2022-12-05

### Fixed

- Fix bug where show grid lines (`setShowGridLines()`) did not account for row/column gaps when positioning items.

## [v0.3.0] - 2022-12-05

### Changed

- Update README.
- Update demo code.

### Added

- Add option to show grid lines while developing. Set `setShowGridLines()` param to `true` and grid lines are drawn when each new grid is defined.

## [v0.2.0] - 2022-12-04

### Changed

- Change `$rGap` and `$cGap` params in `grid()` method to accept either user units (float/int) or percentage (string).
- Change `percentageToFloat()` method to work on single values instead of array and improved validation of percentage values.
- Rewrite README to be clearer (and fixed typos).
- Update demo to reflect class changes.

### Fixed

- Fix issues with float comparisons.

## [v0.1.0] - 2022-12-04

First working version.

[v1.1.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v1.1.0
[v1.0.1]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v1.0.1
[v1.0.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v1.0.0
[v0.3.2]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.3.2
[v0.3.1]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.3.1
[v0.3.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.3.0
[v0.2.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.2.0
[v0.1.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.1.0
