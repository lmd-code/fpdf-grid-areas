# Changelog

## [v0.3.1] - 2022-12-05

### Fixed

- Fixed bug where show grid lines (`setShowGridLines()`) did not account for row/column gaps when positioning items.

## [v0.3.0] - 2022-12-05

### Changed

- Updated README.
- Updated demo code.

### Added

- Added option to show grid lines while developing. Set `setShowGridLines()` param to `true` and grid lines are drawn when each new grid is defined.

## [v0.2.0] - 2022-12-04

### Changed

- Changed `$rGap` and `$cGap` params in `grid()` method to accept either user units (float/int) or percentage (string).
- Changed `percentageToFloat()` method to work on single values instead of array and improved validation of percentage values.
- Rewrote README to be clearer (and fixed typos).
- Updated demo to reflect class changes.

### Fixed

- Fixed issues with float comparisons.

## [v0.1.0] - 2022-12-04

First working version.

[v0.3.1]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.3.1
[v0.3.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.3.0
[v0.2.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.2.0
[v0.1.0]: https://github.com/lmd-code/fpdf-grid-areas/releases/tag/v0.1.0
