<?php declare(strict_types=1);

use Bugo\Bricks\Tables\Row;
use Bugo\Bricks\Tables\RowPosition;
use Nette\Utils\Html;

beforeEach(function () {
	$this->button = Html::el('input', [
		'type' => 'submit',
		'name' => 'button_name',
		'value' => 'Some title',
	])->toHtml();

	$this->row = Row::make($this->button);
});

it('can set a style for the row', function () {
	expect($this->row->setStyle('background: red')->toArray()['style'])->toBe('background: red');
});

it('returns all row data as an array', function () {
	expect($this->row->toArray())->toMatchArray([
		'position' => RowPosition::BELOW_TABLE_DATA->name(),
		'value' => $this->button,
	]);
});
