<?php declare(strict_types=1);

use Bugo\Bricks\Tables\RowPosition;
use Bugo\Bricks\Tables\Row;

/**
 * Row::setPosition() puts these strings into additional_rows[]['position'],
 * and SMF createList() expects exactly these keys.
 */
it('keeps the SMF positioning keys', function () {
	expect(RowPosition::ABOVE_COLUMN_HEADERS->name())->toBe('above_column_headers')
		->and(RowPosition::AFTER_TITLE->name())->toBe('after_title')
		->and(RowPosition::BELOW_TABLE_DATA->name())->toBe('below_table_data')
		->and(RowPosition::BOTTOM_OF_LIST->name())->toBe('bottom_of_list')
		->and(RowPosition::TOP_OF_LIST->name())->toBe('top_of_list');
});

it('keeps the position inside the row data', function () {
	expect(Row::make('value', 'class')->setPosition(RowPosition::ABOVE_COLUMN_HEADERS)->toArray())
		->toBe(['value' => 'value', 'class' => 'class', 'position' => 'above_column_headers']);
});

it('accepts every known SMF position as an enum or string', function (RowPosition $position) {
	expect(Row::make()->setPosition($position)->toArray()['position'])->toBe($position->name())
		->and(Row::make()->setPosition(strtoupper($position->name()))->toArray()['position'])->toBe($position->name());
})->with(RowPosition::cases());

it('rejects unknown positions without changing the row', function () {
	$row = Row::make();

	expect(fn() => $row->setPosition('below_tabel_data'))
		->toThrow(InvalidArgumentException::class, 'Unknown row position "below_tabel_data"')
		->and($row->toArray()['position'])->toBe('below_table_data');
});

it('supports explicit custom positions without normalizing them', function () {
	$row = Row::make();

	expect($row->setCustomPosition('Portal_CustomSlot'))->toBe($row)
		->and($row->toArray()['position'])->toBe('Portal_CustomSlot');
});
