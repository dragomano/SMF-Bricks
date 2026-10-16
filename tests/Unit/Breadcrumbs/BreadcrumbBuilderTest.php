<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbBuilder;
use Bugo\Bricks\Breadcrumbs\BreadcrumbItem;

beforeEach(function () {
	$this->builder = BreadcrumbBuilder::make();
});

it('can add items', function () {
	$this->builder->addItems([
		BreadcrumbItem::make('Home', '/')
			->setBefore('🏠'),
		BreadcrumbItem::make('About', '/about'),
		BreadcrumbItem::make('Contacts', '/contacts')
			->setAfter('✉️'),
	]);

	expect($this->builder->build())->toHaveCount(3);
});

it('can validate items', function () {
	/** @noinspection PhpParamsInspection */
	expect($this->builder->addItems([
		['array instead of BreadcrumbItem class'],
	]))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

describe('updates', function () {
	it('can ignore unknown index', function () {
		$this->builder->add('Foo', '/bar');
		$this->builder->update(10, 'name', 'Bar');

		expect($this->builder->build())->toBe([
			[
				'name' => 'Foo',
				'url' => '/bar',
			]
		]);
	});

	it('can update name', function () {
		$this->builder->add('Foo', '/bar');
		$this->builder->update(0, 'name', 'Bar');

		expect($this->builder->build()[0])->toBe([
			'name' => 'Bar',
			'url' => '/bar',
		]);
	});

	it('can update url', function () {
		$this->builder->add('Foo', '/bar');
		$this->builder->update(0, 'url', '/foo');

		expect($this->builder->build()[0])->toBe([
			'name' => 'Foo',
			'url' => '/foo',
		]);
	});

	it('can update extra before', function () {
		$this->builder->add('Foo', '/bar');
		$this->builder->update(0, 'before', 'extra');

		expect($this->builder->build()[0])->toBe([
			'name' => 'Foo',
			'url' => '/bar',
			'before' => 'extra',
		]);
	});

	it('can update extra after', function () {
		$this->builder->add('Foo', '/bar');
		$this->builder->update(0, 'after', 'extra');

		expect($this->builder->build()[0])->toBe([
			'name' => 'Foo',
			'url' => '/bar',
			'after' => 'extra',
		]);
	});

	it('can ignore unknown key', function () {
		$this->builder->add('Foo', '/bar');
		$this->builder->update(0, 'unknown', 'bla-bla-bla');

		expect($this->builder->build()[0])->toBe([
			'name' => 'Foo',
			'url' => '/bar',
		]);
	});
});

it('can get an item by index', function () {
	$this->builder->add('Foo', '/bar');
	$this->builder->add('Bar', '/foo');

	expect($this->builder->getByIndex(1)['name'])->toBe('Bar');
});

it('can remove an item', function () {
	$this->builder->add('Foo', '/bar');

	expect($this->builder->build())->toHaveCount(1);

	$this->builder->remove(0);

	expect($this->builder->build())->toHaveCount(0);
});

it('skips nameless items without returning string placeholders', function () {
	$builder = BreadcrumbBuilder::make()->add('')->add('Home', '/')->add('', '/empty')->add('About');

	expect($builder->build())->toBe([['name' => 'Home', 'url' => '/'], ['name' => 'About']]);
});

it('keeps a zero breadcrumb alongside valid named entries', function () {
	$builder = BreadcrumbBuilder::make()->add('')->add('0', '0', '0', '0')->add('Home', '/');

	expect($builder->build())->toBe([
		['name' => '0', 'url' => '0', 'before' => '0', 'after' => '0'],
		['name' => 'Home', 'url' => '/'],
	]);
});

it('updates every supported property through the item factory', function (string $key, mixed $value) {
	$item = new class('Original', '/original', 'Before', 'After') extends BreadcrumbItem {
		public function __construct(string $name, ?string $url = null, ?string $before = null, ?string $after = null)
		{
			parent::__construct($name, $url, $before, $after);
		}
	};
	$builder = BreadcrumbBuilder::make()->addItem($item);
	$expected = $item->toArray();
	$expected[$key] = $value;

	expect($builder->update(0, $key, $value))->toBe($builder)
		->and($builder->getAll()[0])->toBeInstanceOf($item::class)->not->toBe($item)
		->and($builder->getByIndex(0))->toBe($expected)
		->and($item->toArray())->toBe(['name' => 'Original', 'url' => '/original', 'before' => 'Before', 'after' => 'After']);
})->with([
	'name' => ['name', 'Updated'],
	'url' => ['url', '/updated'],
	'before' => ['before', 'Updated before'],
	'after' => ['after', 'Updated after'],
]);

it('can clear nullable breadcrumb properties', function (string $key) {
	$builder = BreadcrumbBuilder::make()->add('Home', '/', 'Before', 'After');
	$builder->update(0, $key, null);

	expect($builder->getByIndex(0))->not->toHaveKey($key);
})->with(['url', 'before', 'after']);
