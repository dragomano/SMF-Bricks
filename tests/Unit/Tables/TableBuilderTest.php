<?php declare(strict_types=1);

use Bugo\Bricks\Tables\DateColumn;
use Bugo\Bricks\Tables\IconColumn;
use Bugo\Bricks\Tables\IdColumn;
use Bugo\Bricks\Tables\Row;
use Bugo\Bricks\Tables\RowPosition;
use Bugo\Bricks\Tables\TableBuilder;
use Bugo\Bricks\Tables\Column;

beforeEach(function () {
	$this->builder = TableBuilder::make('table_id', 'Test Table')->name('my_table');
});

it('can set table options', function () {
	$params = [
		'items_per_page' => 10,
		'no_items_label' => 'No items',
		'base_href' => 'https://some.url',
		'default_sort_col' => 'id',
	];
	$this->builder->withParams(...array_values($params));

	$tableData = $this->builder->build();

	expect($tableData['items_per_page'])->toBe($params['items_per_page'])
		->and($tableData['no_items_label'])->toBe($params['no_items_label'])
		->and($tableData['base_href'])->toBe($params['base_href'])
		->and($tableData['default_sort_col'])->toBe($params['default_sort_col']);
});

it('can validate base_href', function () {
	expect($this->builder->setFormAction('not_a_link'))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('can add columns to the table', function () {
	$this->builder->addColumns([
		IdColumn::make(),
		DateColumn::make(),
		IconColumn::make(),
		Column::make('username', 'Username'),
		Column::make('email', 'Email'),
	]);

	expect($this->builder->build()['columns'])->toHaveCount(5);
});

it('can validate columns on adding', function () {
	/** @noinspection PhpParamsInspection */
	expect($this->builder->addColumns([
		['array instead of Column class'],
	]))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('can remove a column from the table', function () {
	$this->builder->addColumn(IdColumn::make());

	expect($this->builder->build()['columns'])->toHaveCount(1);

	$this->builder->removeColumn('id');

	expect($this->builder->build()['columns'])->toHaveCount(0);
});

it('can add rows to the table', function () {
	$this->builder->addRows([
		Row::make('Some row', 'red')->setPosition(RowPosition::TOP_OF_LIST),
		Row::make('One more row', 'green')->setPosition(RowPosition::AFTER_TITLE),
	]);

	expect($this->builder->build()['additional_rows'])->toHaveCount(2);
});

it('can validate rows on adding', function () {
	/** @noinspection PhpParamsInspection */
	expect($this->builder->addRows([
		['array instead of Row class'],
	]))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('can set items from callable', function () {
	$this->builder->setItems(fn() => [
		['username' => 'test_user', 'email' => 'test@example.com'],
		['username' => 'another_user', 'email' => 'another@example.com'],
	]);

	expect($this->builder->build()['get_items'])->toHaveKey('function')
		->and($this->builder->build()['get_items']['function'])->toBeCallable();
});

it('can set items from array', function () {
	$this->builder->setItems([]);

	expect($this->builder->build()['get_items'])->toHaveKey('function')
		->and($this->builder->build()['get_items']['function'])->toBeCallable();
});

it('can set count from callable', function () {
	$this->builder->setCount(fn() => 0);

	expect($this->builder->build()['get_count'])->toHaveKey('function')
		->and($this->builder->build()['get_count']['function'])->toBeCallable();
});

it('can set count from array', function () {
	$this->builder->setCount([]);

	expect($this->builder->build()['get_count'])->toHaveKey('function')
		->and($this->builder->build()['get_count']['function'])->toBeCallable();
});

it('can set count from number', function () {
	$this->builder->setCount(10);

	expect($this->builder->build()['get_count'])->toHaveKey('value')
		->and($this->builder->build()['get_count']['value'])->toBe(10);
});

it('can set javascript', function () {
	$this->builder->setScript('console.log("Hello!")');

	expect($this->builder->build()['javascript'])->toBe('console.log("Hello!")');

	$this->builder->removeScript();

	expect($this->builder->build())->not->toHaveKey('javascript');
});

it('can add hidden fields', function () {
	$this->builder->addHiddenFields(['foo' => 'bar']);

	expect($this->builder->build()['form']['hidden_fields'])->toBe(['foo' => 'bar']);
});

it('can return id', function () {
	expect($this->builder->getId())->toBe('table_id');
});

it('can return title', function () {
	expect($this->builder->getTitle())->toBe('Test Table');
});
