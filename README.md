# SMF Bricks

![SMF 2.1](https://img.shields.io/badge/SMF-2.1-ed6033.svg?style=flat)
![PHP](https://img.shields.io/badge/PHP-^8.2-blue.svg?style=flat)
![Coverage](https://badgen.net/coveralls/c/github/dragomano/SMF-Bricks/main)

[По-русски](README.ru.md)

These helpers will be useful for simplifying the code of tables and forms when creating modifications to [Simple Machines Forum](https://www.simplemachines.org/).

## Installing

In the root directory of your modification, run the command:

```bash
composer require bugo/smf-bricks
```

Then, include `autoload.php` in your code:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## Tables

The simple table builder `TableBuilder::make()` — you just need to provide the data, and you'll get a table styled like on the forum:

![preview](https://github.com/user-attachments/assets/819fe206-08b6-49d1-983b-1b49c0df4ca8)

Anyone who has ever set up a table structure in SMF 2.1 knows that you first create an array called `$listOptions` with the key settings. This array looks bigger the more columns you need to include in it:

```php
<?php

global $context, $sourcedir;

$listOptions = [
    'id' => 'table_id',
    'title' => 'Table title',
    'items_per_page' => 10,
    'no_items_label' => 'There is no data yet',
    'base_href' => $context['canonical_url'],
    'default_sort_col' => 'id',
    'get_items' => [
        'function' => [$this, 'getData'],
    ],
    'get_count' => [
        'function' => [$this, 'getNumData'],
    ],
    'columns' => [
        'id' => [
            'data' => [
                'db' => 'id',
            ],
            'sort' => [
                'default' => 'id DESC',
                'reverse' => 'id',
            ],
        ],
    ],
    'form' => [
        'href' => $context['canonical_url'],
    ],
];

require_once $sourcedir . '/Subs-List.php';

createList($listOptions);

$context['sub_template'] = 'show_list';
$context['default_list'] = 'group_lists';
```

So how can you create such a structure using `TableBuilder`? Thanks to the `fluent interface`, it's very elegant and simple:

```php
$builder = TableBuilder::make('table_id', 'Table title')
    ->withParams(
        perPage: 10,
        noItemsLabel: 'There is no data yet',
        action: $context['canonical_url'],
        defaultSortColumn: 'id'
    )
    ->setItems($this->getData(...)) // You can also use arrays: [$this, 'getData']
    ->setCount($this->getNumData(...)) // You can also use arrays: [$this, 'getNumData']
    ->addColumn(IdColumn::make());
```

We build with the builder, and with the presenter, we display what we've built:

```php
$renderer = new TableRenderer();
$presenter = new TablePresenter($renderer);
$presenter->show($builder);
```

> **Note:** the bundled `TableRenderer` renders a ready-made array (`title`, `headers`, `rows`, `javascript`). Builder output is intended for SMF's `createList()` — see the [Compatibility](#compatibility) section.

Instead of the universal method `withParams`, there are also more specific ones: `paginate`, `setNoItemsLabel`, `setFormAction`, and `setDefaultSortColumn`.

Columns are created using `Column::make()`:

```php
Column::make('column_name', 'Column Header')
    ->setHeaderClass('CSS class for the column header')
    ->setHeaderStyle('CSS style for the column header')
    ->setData('key in the array returned by the getData method', 'CSS class for the data cell')
    ->setSort('column_name', 'column_name DESC'),
```

`setClass()` and `setStyle()` are still supported and also apply to the header. Use the class/style arguments of `setData()` for data cells.

There are also several child methods that inherit from `Column`: `IdColumn`, `CheckboxColumn`. You can easily create similar ones for your projects. For example, `IdColumn` is just a wrapper for a structure like:

```php
Column::make('id', '#')
    ->setStyle('width: 5%')
    ->setData('id', '', 'text-align: center')
    ->setSort('id DESC', 'id');
```

When adding multiple columns, you can use the method `addColumns([Column::make(), Column::make(), ...])`, which accepts an array of `Column` instances.

You can also add buttons ABOVE or BELOW the table using the methods `addRows([Row::make($button1), Row::make($button2)])` or `addRow(Row::make($button))`, where `Row::make` accepts text or HTML. The position of the buttons can be changed using the `setPosition` method, which takes one of the values from the `RowPosition` enumeration. By default, the buttons are displayed below the table.

## Forms

What could be more interesting than building all sorts of forms, especially in SMF? Of course, creating and using a `FormBuilder` for such forms:

```php
$builder = FormBuilder::make('form_id', 'Form title')
	->setBodyClass('windowbg')
    ->setAction(Utils::$context['post_url'] ?? '');
```

You can add form fields in a container (by specifying the block class as the second parameter):

```php
$builder->addFields([
    TextField::make('title', 'Title')
        ->setStyle('width: 100%')
        ->required()
        ->setPlaceholder('Enter title'),
    TextareaField::make('description', 'Description')
        ->setPlaceholder('Some description')
        ->setStyle('width: 100%'),
    SelectField::make('status', 'Status')
        ->setValue('inactive')
        ->setOptions([
            'active' => 'Active',
            'inactive' => 'Inactive'
        ]),
], 'roundframe');
```

Or without it:

```php
$builder->addFields([
    ColorField::make('color', 'Color')
        ->setValue('#ff00dd'),
    EmailField::make('email', 'Email')
        ->setValue('no-reply@no-reply.com'),
    NumberField::make('number', 'Number')
        ->setValue(0)
        ->setMin(0),
    PasswordField::make('password', 'Password')
        ->readonly()
        ->setValue('password'),
    RangeField::make('range', 'Range')
        ->setValue(1)
        ->setMin(0)
        ->setMax(3)
        ->setStep(1),
    UrlField::make('url', 'URL')
        ->disabled()
        ->setSize(40)
        ->setValue('https://github.com/dragomano/SMF-Bricks'),
]);
```

You can add your own buttons at the bottom of the form, specifying a class for the container as well as a class for all buttons inside the container:

```php
$builder->addButtons([
    ResetButton::make(),
    Button::make('button_name', 'Click me'),
    SubmitButton::make(),
], 'floatright', 'button');
```

You can also add hidden fields:

```php
$builder->addHiddenFields([
    Utils::$context['session_var'] => Utils::$context['session_id']
]);
```

Of course, there is a dedicated presenter for `FormBuilder` — `FormPresenter`, designed to display the built forms in the browser:

```php
$renderer = new FormRenderer();
$presenter = new FormPresenter($renderer);
$presenter->show($builder);
```

![preview](https://github.com/user-attachments/assets/c326e937-f11d-443c-ab96-a4b489a69dbb)

## Settings

We’re excited to share some good news for those who find working with arrays while creating settings a bit tedious. For instance, the Light Portal settings block on the "Miscellaneous" tab was previously built like this:

```php
$configVars = [
    ['title', 'lp_debug_and_caching'],
    ['check', 'lp_show_debug_info', 'help' => 'lp_show_debug_info_help'],
    ['int', 'lp_cache_interval', 'postinput' => Lang::$txt['seconds']],
    ['title', 'lp_compatibility_mode'],
    [
        'text',
        'lp_portal_action',
        'subtext' => Config::$scripturl . '?action=<strong>' . LP_ACTION . '</strong>'
    ],
    [
        'text',
        'lp_page_param',
        'subtext' => Config::$scripturl . '?<strong>' . LP_PAGE_PARAM . '</strong>=page_slug'
    ],
    ['title', 'admin_maintenance'],
    ['check', 'lp_weekly_cleaning']
];
```

Now, thanks to `ConfigBuilder` and a set of child classes from `AbstractConfig`, everything looks much more interesting:

```php
$vars = ConfigBuilder::make()->addVars([
    TitleConfig::make('lp_debug_and_caching'),
    CheckConfig::make('lp_show_debug_info')
        ->setHelp('lp_show_debug_info_help'),
    IntConfig::make('lp_cache_interval')
        ->setPostInput(Lang::$txt['seconds']),
    TitleConfig::make('lp_compatibility_mode'),
    TextConfig::make('lp_portal_action')
        ->setSubText(Config::$scripturl . '?action=<strong>' . LP_ACTION . '</strong>'),
    TextConfig::make('lp_page_param')
        ->setSubText(Config::$scripturl . '?<strong>' . LP_PAGE_PARAM . '</strong>=page_slug'),
    TitleConfig::make('admin_maintenance'),
    CheckConfig::make('lp_weekly_cleaning'),
]);

$configVars = $vars->build();
```

## Breadcrumbs

You can add your own navigation elements using `BreadcrumbBuilder`:

```php
$breadcrumbs = BreadcrumbBuilder::make()->addItems([
    BreadcrumbItem::make('Home', '/')
        ->setBefore('🏠'),
    BreadcrumbItem::make('About', '/about'),
    BreadcrumbItem::make('Contacts', '/contacts')
        ->setAfter('✉️'),
]);

$renderer = new BreadcrumbRenderer();
$presenter = new BreadcrumbPresenter($renderer);
$presenter->show($breadcrumbs);
```

## Compatibility

The package is a dependency of [Light Portal](https://github.com/dragomano/Light-Portal), so its public API and the structure of `build()`/`toArray()` results are treated as a contract:

- `TableBuilder::build()` — an array of SMF `createList()` options (`columns`, `default_sort_col`, `get_items`, `get_count`, `items_per_page`, `form`, `additional_rows`, `javascript` and others); the presenter only applies `excludeColumns()`, so plug in your own renderer;
- `*Config::toArray()` — an SMF config var: the type at index `0`, the name at index `1`, options at index `2`, plus keys such as `tab`, `attributes`, `callback`, `placeholder`, `min`, `max`, `step`;
- `BreadcrumbBuilder::build()` — an array of arrays with the `name`, `url`, `before` and `after` keys;
- `RowPosition::name()` — its values are used as SMF `additional_rows` position keys;
- `RendererInterface` — implemented by third-party renderers, so it intentionally has a single `render(array $data): void` method;
- `make()` uses late static binding (`new static`), and `TableBuilder::__construct()` stays `protected`, so subclasses (like `PortalTableBuilder`) keep working.

Changes are additive: keys, method names, parameter names (they may be passed as named arguments) and enum values are not renamed. Breaking changes are released as `0.10+` and require updating the consumer first.

### Upgrading to 0.10

- The unused root `PresenterInterface` has been removed. Use the form, table or breadcrumb presenter interfaces.
- The bundled renderers implement `StringRendererInterface`: `render(array $data): string` returns markup. Use `echo $renderer->render($data)` for direct output, or keep using `Presenter::show()`. Presenters also accept existing `RendererInterface` implementations, whose `render(): void` writes to the output.
- `Column` subclasses can call the protected constructor and extend the protected `$attributes` array; `make()` continues to return the subclass.
- Builder `addField()`, `addButton()`, `addColumn()`, `addRow()`, `addItem()` and `addVar()` contracts accept their corresponding item interfaces.
- Breadcrumb updates rebuild every supported property through the item's own factory, preserving its class; empty names are skipped by `build()`. The bundled renderer accepts builder arrays and item objects. Builder output contains only named arrays, which external renderers can consume directly; the string `'0'` is preserved.
- `Row::setPosition()` validates SMF positions and accepts enum cases or their strings. Use `Row::setCustomPosition('custom_slot')` for positions supplied by custom renderers.
- `DividerConfig` keeps its existing output. Changing it requires removing positional config indexing in Light Portal first.

### HTML and text

The default `TableRenderer` treats the title, header labels and cell values as trusted HTML. Use `new TableRenderer(escapeData: true)` to escape these values as text; this renders ready-made `headers`/`rows` arrays, as described above.

Breadcrumb names, form labels and field values are escaped as text. Breadcrumb `before`/`after` fragments and form scripts remain trusted markup/code. `Field::setAttributes()` accepts arbitrary attributes, including event handlers; pass attribute names and scripts from trusted application code. Text escaping does not validate URLs or sanitize scripts and HTML fragments.

More usage examples can be found in the tests, as well as in the [Light Portal](https://github.com/dragomano/Light-Portal/tree/master/src/Sources/LightPortal) project.
