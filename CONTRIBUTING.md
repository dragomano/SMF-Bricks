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
composer analyse                 # PHPStan, level 9 with phpstan-strict-rules
composer test-coverage-clover    # the test suite with the 100% coverage gate
```

All of them must pass before a change is submitted. The suite is expected to stay at 100% line
coverage, and new behaviour needs new tests — see `tests/Unit/**/*ContractTest.php` for the tests that
pin the public API down.

The PHPStan baseline contains one deliberate exception: `method.dynamicName` in
`HasShortMethods::__call()`. This trait dispatches fluent aliases to their setters in six classes.
The exception is restricted to that file and those six occurrences; the former `new.static`
entries are no longer needed.

## Mutation testing

Enable Xdebug with `XDEBUG_MODE=coverage` or PCOV in CLI PHP, then run:

```bash
composer test-mutate
composer test-mutate -- --filter=src/Forms
```

PHP must also be available to child processes. When using `PHP_INI_SCAN_DIR`, keep the coverage
extension enabled there so the initial test process inherits it.

Infection runs the Pest suite through `tests/infection.php`. This adapter reports the installed
PHPUnit version and the actual Pest test result in PHPUnit's output format. It also restores
Pest's generated class names and absolute test paths in the JUnit report used by Infection.

Review `.infection/infection.log` for escaped mutants, `.infection/infection.json` for the machine
readable report, and `.infection/summary.log` for totals. Reports and temporary files are ignored
by Git. CI runs mutation testing once on PHP 8.2, after the PHP test matrix and the coverage
check, with an MSI threshold of 95%:

```bash
composer test-mutate -- --min-msi=95
```

The `mutation-reports` CI artifact contains the three reports, including when the mutation
check fails. Mutation testing is not repeated across the PHP version matrix.

To check the runner after changing its configuration, run:

```bash
composer test-mutate -- --noop --filter=src/Breadcrumbs/BreadcrumbItem.php
```

All control mutants must escape: `--noop` leaves the source behaviour unchanged.

### Reviewed equivalent mutations

The unfiltered run on 2026-10-16 leaves 22 equivalent mutations for supported input values:

| Mutator | Count | Reason |
| --- | ---: | --- |
| `CastBool` | 16 | An `if` condition already converts its operand to a boolean. |
| `CastString` | 2 | Script concatenation and `implode()` already convert their validated operands to strings. |
| `FalseValue` | 2 | Without a selected value, neither boolean sentinel can strictly match an integer or string option key. |
| `UnwrapUcFirst` | 1 | PHP method lookup is case insensitive, including the setter lookup in `HasShortMethods`. |
| `IncrementInteger` | 1 | `AbstractConfig::toArray()` merges its numeric entries with `array_merge()`, which renumbers both the original name index and its mutated index to `1`. |

These mutations remain enabled and visible in the reports; the raw MSI is 96.69%.
The six `Errored` mutants make public trait methods protected, contradicting the interfaces of
their consuming classes. PHP rejects those classes during loading. They are detected invalid
mutants, rather than failures in the original source. The unmodified test suite and the `--noop`
control run must still pass independently.

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
