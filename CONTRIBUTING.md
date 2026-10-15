# Contributing

## Requirements

- PHP 8.2 or newer (CI also runs 8.3, 8.4 and 8.5);
- Composer 2.

## Getting started

```bash
composer install
```

## Checks

```bash
composer test                    # Pest test suite
composer check                   # Rector (dry run)
composer analyse                 # PHPStan, level 8
composer test-coverage-clover    # the test suite with the 100% coverage gate
```

All of them must pass before a change is submitted. The suite is expected to stay at 100% line
coverage, and new behaviour needs new tests — see `tests/Unit/**/*ContractTest.php` for the tests that
pin the public API down.

## Compatibility rules

The package is used by [Light Portal](https://github.com/dragomano/Light-Portal), and its
`build()`/`toArray()` results are consumed by SMF and by portal code. Therefore:

- public API changes are additive: keys, method names, parameter names (they may be passed as named
  arguments) and enum values are not renamed;
- the keys of `TableBuilder::build()` are SMF `createList()` options, and the keys of
  `*Config::toArray()` are SMF config vars (including the positional `[0]`, `[1]` and `[2]` entries) —
  both must stay stable;
- `RendererInterface` is implemented by third-party renderers, so new capabilities belong to a new
  interface rather than to this one;
- `make()` keeps late static binding (`new static`), and `TableBuilder::__construct()` stays
  `protected`, so subclasses such as `PortalTableBuilder` keep working;
- breaking changes are released as `0.10+` and are announced in the changelog together with the
  consumer update.

## Code style

Tabs for indentation, `declare(strict_types=1)` in every file, PSR-4 autoloading
(`Bugo\Bricks\` → `src/`). Rector keeps the style consistent, so run `composer check` before
submitting a change.
