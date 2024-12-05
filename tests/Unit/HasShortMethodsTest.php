<?php declare(strict_types=1);

use Bugo\Bricks\Tables\Row;

beforeEach(function () {
	$this->row = Row::make('some row');
});

it('can provide short variants of known methods', function () {
	expect($this->row->class('btn btn-button')->toArray()['class'])->toBe('btn btn-button')
		->and($this->row->style('height: 100px')->toArray()['style'])->toBe('height: 100px');
});

it('cannot provide short variants of unknown methods', function () {
	expect($this->row->blablabla('test'))->toThrow(BadMethodCallException::class);
})->throws(BadMethodCallException::class);
