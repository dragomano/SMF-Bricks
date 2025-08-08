# SMF Bricks

![SMF 2.1](https://img.shields.io/badge/SMF-2.1-ed6033.svg?style=flat)
![PHP](https://img.shields.io/badge/PHP-^8.1-blue.svg?style=flat)
![Coverage](https://badgen.net/coveralls/c/github/dragomano/SMF-Bricks/main)

[English](README.md)

Эти хелперы пригодятся для упрощения написания кода таблиц и форм при создании модификаций для [Simple Machines Forum](https://www.simplemachines.org/).

## Установка

В корневой директории вашей модификации выполните команду:

```bash
composer require bugo/smf-bricks
```

Затем в вашем коде подключите `autoload.php`:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## Таблицы

Скромный строитель таблиц `TableBuilder::make()` — вам нужно лишь указать данные, а на выходе вы получите таблицу с оформлением как на форуме:

![preview](https://github.com/user-attachments/assets/36beded9-2aa9-4bcb-9b3d-f2b11ff5801a)

Кто хоть раз формировал структуру таблицы в SMF 2.1, знает, что там сначала создаётся массив `$listOptions` с указанием ключевых настроек. Выглядит подобный массив тем больше, чем больше столбцов вам нужно в него запихнуть:

```php
<?php

global $context, $sourcedir;

$listOptions = [
    'id' => 'table_id',
    'title' => 'Заголовок таблицы',
    'items_per_page' => 10,
    'no_items_label' => 'Данных пока нет',
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

Как же такую структуру можно сформировать с помощью `TableBuilder`? Благодаря `текучему интерфейсу` очень элегантно и просто:

```php
$builder = TableBuilder::make('table_id', 'Заголовок таблицы')
    ->withParams(
        perPage: 10,
        noItemsLabel: 'Данных пока нет',
        action: $context['canonical_url'],
        defaultSortColumn: 'id'
    )
    ->setItems($this->getData(...)) // Можно и так: [$this, 'getData']
    ->setCount($this->getNumData(...)) // Можно и так: [$this, 'getNumData']
    ->addColumn(IdColumn::make());
```

С помощью строителя строим, с помощью презентера - отображаем то, что построили:

```php
$renderer = new TableRenderer();
$presenter = new TablePresenter($renderer);
$presenter->show($builder);
```

Вместо универсального метода `withParams` есть также более точечные: `paginate`, `setNoItemsLabel`, `setFormAction` и `setDefaultSortColumn`.

Столбцы формируются с помощью `Column::make()`:

```php
Column::make('column_name', 'Заголовок столбца')
    ->setClass('CSS класс для столбца')
    ->setStyle('CSS для столбца')
    ->setData('ключ в массиве, возвращаемом методом getData', 'CSS класс для ячейки данных')
    ->setSort('column_name', 'column_name DESC'),
```

Также есть несколько дочерних методов-наследников от `Column`: `IdColumn`, `CheckboxColumn`. Вы легко можете создать аналогичные для своих проектов. Например, `IdColumn` лишь обёртка для конструкции вида:

```php
Column::make('id', '#')
    ->setStyle('width: 5%')
    ->setData('id', '', 'text-align: center')
    ->setSort('id DESC', 'id');
```

При добавлении нескольких столбцов можно использовать метод `addColumns([Column::make(), Column::make(), ...])`, принимающий массив экземпляров `Column`.

Вы также можете добавлять кнопки НАД или ПОД таблицей, с помощью методов `addRows([Row::make($button1), Row::make($button2)])` или `addRow(Row::make($button))`, где `Row::make` принимает текст или HTML. Расположение кнопок можно изменить с помощью метода `setPosition`, принимающего одно из значений перечисления `RowPosition`. По умолчанию кнопки отображаются под таблицей.

## Формы

Что может быть интересней построения всевозможных форм, особенно в SMF? Конечно же, создать и использовать конструктор `FormBuilder` для таких форм:

```php
$builder = FormBuilder::make('form_id', 'Заголовок формы')
	->setBodyClass('windowbg')
    ->setAction(Utils::$context['post_url'] ?? '');
```

Вы можете добавлять поля формы в контейнере (указав класс блока вторым параметром):

```php
$builder->addFields([
    TextField::make('title', 'Заголовок')
        ->setStyle('width: 100%')
        ->required()
        ->setPlaceholder('Введите заголовок'),
    TextareaField::make('description', 'Описание')
        ->setPlaceholder('Какое-нибудь описание')
        ->setStyle('width: 100%'),
    SelectField::make('status', 'Статус')
        ->setValue('inactive')
        ->setOptions([
            'active' => 'Активный',
            'inactive' => 'Неактивный'
        ]),
], 'roundframe');
```

Или без него:

```php
$builder->addFields([
    ColorField::make('color', 'Цвет')
        ->setValue('#ff00dd'),
    EmailField::make('email', 'Имейл')
        ->setValue('no-reply@no-reply.com'),
    NumberField::make('number', 'Число')
        ->setValue(0)
        ->setMin(0),
    PasswordField::make('password', 'Пароль')
        ->readonly()
        ->setValue('password'),
    RangeField::make('range', 'Диапазон')
        ->setValue(1)
        ->setMin(0)
        ->setMax(3)
        ->setStep(1),
    UrlField::make('url', 'URL-адрес')
        ->disabled()
        ->setSize(40)
        ->setValue('https://github.com/dragomano/SMF-Bricks'),
]);
```

Можно добавлять свои кнопки в нижней части формы, с указанием класса для контейнера, а также класса для всех кнопок внутри контейнера:

```php
$builder->addButtons([
    ResetButton::make(),
    Button::make('button_name', 'Нажми меня'),
    SubmitButton::make(),
], 'floatright', 'button');
```

А ещё можно добавлять скрытые поля:

```php
$builder->addHiddenFields([
    Utils::$context['session_var'] => Utils::$context['session_id']
]);
```

Конечно же, для `FormBuilder` есть собственный презентер — `FormPresenter`, предназначенный для отображения сформированных форм в браузере:

```php
$renderer = new FormRenderer();
$presenter = new FormPresenter($renderer);
$presenter->show($builder);
```

![preview](https://github.com/user-attachments/assets/3355fa0f-4f6d-47b6-a6d9-9650f7260427)

## Настройки

Мы можем обрадовать и тех, кто устал работать с массивами при создании настроек. Например, блок настроек Light Portal на вкладке «Дополнительно» раньше формировался вот так:

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

Теперь же, благодаря `ConfigBuilder` и набору дочерних классов `AbstractConfig`, всё выглядит намного интереснее:

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

## Хлебные крошки

Добавить свои элементы навигации можно с помощью конструктора `BreadcrumbBuilder`:

```php
$breadcrumbs = BreadcrumbBuilder::make()->addItems([
    BreadcrumbItem::make('Главная', '/')
        ->setBefore('🏠'),
    BreadcrumbItem::make('О сайте', '/about'),
    BreadcrumbItem::make('Контакты', '/contacts')
        ->setAfter('✉️'),
]);

$renderer = new BreadcrumbRenderer();
$presenter = new BreadcrumbPresenter($renderer);
$presenter->show($breadcrumbs);
```

Больше примеров использования можно найти в тестах, а также в проекте [Light Portal](https://github.com/dragomano/Light-Portal/tree/master/src/Sources/LightPortal)
