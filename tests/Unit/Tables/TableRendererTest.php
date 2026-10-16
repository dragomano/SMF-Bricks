<?php declare(strict_types=1);

use Bugo\Bricks\Tables\TableRenderer;

it('renders a cell without a value key', function () {
	$data = [
		'id'   => 'table_id',
		'rows' => [
			['data' => [['class' => 'some_class']]],
		],
	];

	$output = (new TableRenderer())->render($data);

	expect($output)->toContain('<td class="some_class"></td>');
});

it('keeps trusted HTML by default and can escape table text explicitly', function () {
	$data = [
		'id'      => 'table_id',
		'title'   => '<b>Title</b>',
		'headers' => [['id' => 'name', 'label' => '<i>Name</i>']],
		'rows'    => [['data' => [['value' => '<em>A & B</em>']]]],
	];

	$html = (new TableRenderer())->render($data);
	$text = (new TableRenderer(escapeData: true))->render($data);

	expect($html)->toContain('<b>Title</b>', '<i>Name</i>', '<em>A & B</em>')
		->and($text)->toContain('&lt;b&gt;Title&lt;/b&gt;', '&lt;i&gt;Name&lt;/i&gt;', '&lt;em&gt;A &amp; B&lt;/em&gt;');
});

it('renders scalar and stringable values and treats unsupported values as empty text', function () {
	$data = [
		'id'   => 'table_id',
		'rows' => [['data' => [
			['value' => 0],
			['value' => new class implements Stringable {
				public function __toString(): string
				{
					return 'Stringable value';
				}
			}],
			['value' => []],
		]]],
	];

	expect((new TableRenderer())->render($data))
		->toContain('<td>0</td><td>Stringable value</td><td></td>');
});
