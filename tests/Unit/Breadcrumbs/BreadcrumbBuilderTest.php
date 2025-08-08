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

it('can remove an item', function () {
    $this->builder->add('Foo', '/bar');

    expect($this->builder->build())->toHaveCount(1);

    $this->builder->remove(0);

    expect($this->builder->build())->toHaveCount(0);
});

it('can clear all items', function () {
    $this->builder->add('Foo', '/bar');
    $this->builder->add('Bar', '/foo');

    expect($this->builder->build())->toHaveCount(2);

    $this->builder->clear();

    expect($this->builder->build())->toHaveCount(0);
});

