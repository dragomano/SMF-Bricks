# SMF Bricks

![SMF 2.1](https://img.shields.io/badge/SMF-2.1-ed6033.svg?style=flat)
![PHP](https://img.shields.io/badge/PHP-^8.1-blue.svg?style=flat)

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
TablePresenter::show($builder);
```

The current presenter for tables is still tightly bound to SMF, so you can't build the table outside the forum.

Instead of the universal method `withParams`, there are also more specific ones: `paginate`, `setNoItemsLabel`, `setFormAction`, and `setDefaultSortColumn`.

Columns are created using `Column::make()`:

```php
Column::make('column_name', 'Column Header')
    ->setClass('CSS class for the column')
    ->setStyle('CSS style for the column')
    ->setData('key in the array returned by the getData method', 'CSS class for the data cell')
    ->setSort('column_name', 'column_name DESC'),
```

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
FormPresenter::show($builder);
```

![preview](https://github.com/user-attachments/assets/c326e937-f11d-443c-ab96-a4b489a69dbb)

More usage examples can be found in the tests, as well as in the [Light Portal](https://github.com/dragomano/Light-Portal/tree/master/src/Sources/LightPortal) project.

## In Development (contributions are welcome)

- [ ] ApiTablePresenter
- [x] CsvTablePresenter
- [x] JsonTablePresenter
- [x] XmlTablePresenter
