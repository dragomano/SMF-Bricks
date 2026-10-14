<?php declare(strict_types=1);

use Bugo\Bricks\Tables\IdColumn;
use Bugo\Bricks\Tables\Row;
use Bugo\Bricks\Tables\TableBuilder;
use Tests\Unit\Tables\SubclassedTableBuilder;

/**
 * The output of TableBuilder::build() is passed to SMF createList() (Light Portal
 * does it through Bugo\Compat\ItemList), so its keys are a public contract.
 */
it('keeps the build() key set stable', function () {
	$data = TableBuilder::make('table_id', 'Title')
		->setName('form_name')
		->paginate(20)
		->setNoItemsLabel('There is no data yet')
		->setFormAction('https://example.com/index.php?action=admin')
		->setDefaultSortColumn('id')
		->setItems(fn($start, $limit, $sort) => [], ['param' => 1])
		->setCount(fn() => 0, ['param' => 1])
		->addColumn(IdColumn::make())
		->addFormData(['extra' => '1'])
		->addHiddenFields(['sc' => 'abc'])
		->addRows([Row::make('<b>row</b>')])
		->setScript('console.log(1)')
		->build();

	$keys = array_keys($data);
	sort($keys);

	expect($keys)->toBe([
		'additional_rows',
		'base_href',
		'columns',
		'default_sort_col',
		'form',
		'get_count',
		'get_items',
		'id',
		'items_per_page',
		'javascript',
		'no_items_label',
		'title',
	]);

	$formKeys = array_keys($data['form']);
	sort($formKeys);

	expect($formKeys)->toBe(['extra', 'hidden_fields', 'href', 'name'])
		->and(array_keys($data['columns']['id']))->toBe(['header', 'data', 'sort'])
		->and(array_keys($data['get_items']))->toBe(['function', 'params'])
		->and(array_keys($data['get_count']))->toBe(['function', 'params'])
		->and(array_keys($data['additional_rows'][0]))->toBe(['value', 'class', 'position'])
		->and($data['additional_rows'][0]['position'])->toBe('below_table_data');
});

it('supports subclassing the way Light Portal does', function () {
	$builder = SubclassedTableBuilder::make('lp_pages', 'Pages');

	expect($builder)->toBeInstanceOf(SubclassedTableBuilder::class)
		->and($builder->getId())->toBe('lp_pages')
		->and($builder->getTitle())->toBe('Pages')
		->and($builder->build()['items_per_page'])->toBe(20)
		->and($builder->build()['no_items_label'])->toBe('There is no data yet')
		->and($builder->withTitleSuffix(' +'))->toBe($builder)
		->and($builder->getTitle())->toBe('Pages +');
});

it('accepts named arguments for withParams()', function () {
	$data = TableBuilder::make('table_id', 'Title')
		->withParams(
			perPage: 50,
			noItemsLabel: 'There is no data yet',
			action: 'https://example.com/index.php?action=admin',
			defaultSortColumn: 'id'
		)
		->build();

	expect($data['items_per_page'])->toBe(50)
		->and($data['no_items_label'])->toBe('There is no data yet')
		->and($data['base_href'])->toBe('https://example.com/index.php?action=admin')
		->and($data['default_sort_col'])->toBe('id');
});
