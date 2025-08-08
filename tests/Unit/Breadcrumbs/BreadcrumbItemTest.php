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
