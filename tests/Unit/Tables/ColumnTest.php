<?php declare(strict_types=1);

use Bugo\Bricks\Tables\Column;

beforeEach(function () {
	$this->column = Column::make('username', 'Username');
});

it('can create a column with a name and title', function () {
	expect($this->column->getName())->toBe('username')
		->and($this->column->toArray())->toMatchArray([
			'header' => [
				'value' => 'Username',
			]
		]);
});

it('can set the class of the column', function () {
	expect($this->column->setClass('text-center')->toArray())->toMatchArray([
		'header' => [
			'value' => 'Username',
			'class' => 'text-center',
		]
	]);
});

it('can set the style of the column', function () {
	expect($this->column->setStyle('width: 150px')->toArray())->toMatchArray([
		'header' => [
			'value' => 'Username',
			'style' => 'width: 150px',
		]
	]);
});

it('can set the data of the column', function () {
	$data = [
		'closure' => function ($entry) { return $entry['id']; },
		'string'  => '',
		'array'   => [],
	];

	expect($this->column->setData($data['closure'])->toArray()['data'])->toHaveKey('function')
		->and($this->column->setData($data['string'])->toArray()['data'])->toHaveKey('db')
		->and($this->column->setData($data['array'])->toArray()['data'])->toMatchArray($data['array']);
});

it('can set the data with css class', function () {
	expect($this->column->setData('test', 'some_class')->toArray()['data']['class'])
		->toBe('some_class');
});

it('returns all attributes as an array', function () {
	$column = $this->column
		->setClass('text-center')
		->setSort('user');

	expect($column->toArray())->toMatchArray([
		'header' => [
			'value' => 'Username',
			'class' => 'text-center',
		],
		'sort' => [
			'default' => 'user',
			'reverse' => 'user DESC',
		]
	]);
});

it('provides header aliases without changing data cell attributes', function () {
	$column = Column::make('name', 'Name')
		->setHeaderClass('header')
		->setHeaderStyle('width: 20%')
		->setData('name', 'cell', 'text-align: right');

	expect($column->toArray())->toBe([
		'header' => ['value' => 'Name', 'class' => 'header', 'style' => 'width: 20%'],
		'data' => ['db' => 'name', 'class' => 'cell', 'style' => 'text-align: right'],
	]);
});

it('allows subclasses to call the constructor and extend the attributes', function () {
	$column = new class('custom', 'Custom') extends Column {
		public function __construct(string $name, string $title)
		{
			parent::__construct($name, $title);
			$this->attributes['data'] = ['db' => $name];
		}
	};

	$created = $column::make('id', '#');

	expect($created)->toBeInstanceOf($column::class)
		->and($created->getName())->toBe('id')
		->and($created->toArray())->toBe(['header' => ['value' => '#'], 'data' => ['db' => 'id']]);
});
