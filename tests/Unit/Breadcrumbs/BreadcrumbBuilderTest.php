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
