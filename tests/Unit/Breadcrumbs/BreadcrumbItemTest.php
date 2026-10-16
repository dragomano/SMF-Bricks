<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbItem;

beforeEach(function () {
	$this->item = BreadcrumbItem::make('Foo', '/bar');
});

it('can create an item with a name and url', function () {
	expect($this->item->getName())->toBe('Foo')
		->and($this->item->toArray())->toMatchArray([
			'name' => 'Foo',
			'url' => '/bar',
		]);
});

it('preserves zero strings in breadcrumb properties', function () {
	expect(BreadcrumbItem::make('0', '0', '0', '0')->toArray())
		->toBe(['name' => '0', 'url' => '0', 'before' => '0', 'after' => '0']);
});

it('omits only null and empty strings', function () {
	expect(BreadcrumbItem::make('Home', null, '', null)->toArray())->toBe(['name' => 'Home']);
});
