<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbBuilder;
use Bugo\Bricks\Breadcrumbs\BreadcrumbRenderer;
use Bugo\Bricks\Forms\Button;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\FormRenderer;
use Bugo\Bricks\Forms\RequestData;
use Bugo\Bricks\Forms\SelectField;
use Bugo\Bricks\Forms\TextField;
use Bugo\Bricks\Settings\TextConfig;
use Bugo\Bricks\Tables\Column;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Bricks\Tables\Row;
use Bugo\Bricks\Tables\TableBuilder;
use Bugo\Bricks\Tables\TablePresenter;
use Bugo\Bricks\Tables\TableRenderer;

it('rejects invalid named attributes supplied in bulk', function (string $class, string $attribute, string $getter) {
	$item = $class::make('name', 'Label')->setAttributes([$attribute => []]);

	expect(fn() => $item->$getter())->toThrow(InvalidArgumentException::class);
})->with([
	'field name'  => [Field::class, 'name', 'getName'],
	'field type'  => [Field::class, 'type', 'getType'],
	'button name' => [Button::class, 'name', 'getName'],
	'button type' => [Button::class, 'type', 'getType'],
]);

it('rejects invalid option lists and returns an empty list before options are set', function () {
	$field = SelectField::make('choice', 'Choice');
	expect($field->getOptions())->toBe([]);

	$field->setAttributes(['options' => 'invalid']);
	expect(fn() => $field->getOptions())->toThrow(InvalidArgumentException::class);
});

it('normalizes scalar and stringable single select values', function () {
	$value = new class implements Stringable {
		public function __toString(): string
		{
			return 'custom';
		}
	};

	expect(SelectField::make('choice', 'Choice')->setValue([0, false, null, $value])->getValue())
		->toBe('0,,,custom');
});

it('rejects nested single select values', function () {
	expect(fn() => SelectField::make('choice', 'Choice')->setValue([['nested']]))
		->toThrow(InvalidArgumentException::class);
});

it('rejects non-string setting attributes', function () {
	expect(fn() => TextConfig::make('setting')->setAttributes(['placeholder' => []]))
		->toThrow(InvalidArgumentException::class);
});

it('converts scalar and stringable breadcrumb updates and rejects arrays', function () {
	$value = new class implements Stringable {
		public function __toString(): string
		{
			return 'custom';
		}
	};

	$builder = BreadcrumbBuilder::make()->add('Home');
	$builder->update(0, 'name', 0)->update(0, 'url', $value);

	expect($builder->build()[0])->toMatchArray(['name' => '0', 'url' => 'custom'])
		->and(fn() => $builder->update(0, 'name', []))->toThrow(InvalidArgumentException::class);
});

it('rejects invalid breadcrumb markup', function () {
	expect(fn() => (new BreadcrumbRenderer())->render([['name' => 'Home', 'before' => []]]))
		->toThrow(InvalidArgumentException::class);
});

it('rejects invalid form collections', function (string $key) {
	expect(fn() => (new FormRenderer())->render(['id' => 'form', 'title' => '', $key => 'invalid']))
		->toThrow(InvalidArgumentException::class);
})->with(['fields', 'buttons']);

it('renders select option attributes and numeric labels', function () {
	$builder = FormBuilder::make('form')->addField(
		SelectField::make('choice', 'Choice')->setOptions(['custom' => ['class' => 'muted'], 'zero' => 0])
	);

	$html = (new FormRenderer())->render($builder->build());

	expect($html)->toContain('<option class="muted" value="custom"></option>', '<option value="zero">0</option>');
});

it('rejects invalid form text', function () {
	$builder = FormBuilder::make('form')->addField(TextField::make('text', 'Text')->setAttributes(['label' => []]));

	expect(fn() => (new FormRenderer())->render($builder->build()))->toThrow(InvalidArgumentException::class);
});

it('applies classes to fields and buttons in groups', function () {
	$builder = FormBuilder::make('form')
		->addFields([TextField::make('text', 'Text')], fieldClass: 'field-class')
		->addButtons([Button::make('save', 'Save')], buttonClass: 'button-class');

	$data = $builder->build();

	expect($data['fields']['text']->toArray()['class'])->toBe('field-class')
		->and($data['buttons']['save']->toArray()['class'])->toBe('button-class');
});

it('falls back to GET for an invalid server request method', function () {
	$server = $_SERVER;

	try {
		$_SERVER['REQUEST_METHOD'] = [];
		expect(RequestData::fromGlobals()->method)->toBe('GET');
	} finally {
		$_SERVER = $server;
	}
});

it('rejects unsupported count values and malformed hidden fields', function () {
	$builder = TableBuilder::make('table', 'Table');
	expect(fn() => $builder->setCount(new stdClass()))->toThrow(InvalidArgumentException::class);

	$builder->addFormData(['hidden_fields' => 'invalid']);
	expect(fn() => $builder->addHiddenFields(['token' => 'value']))->toThrow(InvalidArgumentException::class);
});

it('applies classes and styles to table columns and rows', function () {
	$data = TableBuilder::make('table', 'Table')
		->addColumns([Column::make('name', 'Name')], 'column-class', 'width: 5%')
		->addRows([Row::make('Row')], 'row-class', 'color: red')
		->addRow(Row::make('Footer'), 'footer-class')
		->build();

	expect($data['columns']['name']['header'])->toMatchArray(['class' => 'column-class', 'style' => 'width: 5%'])
		->and($data['additional_rows'][0])->toMatchArray(['class' => 'row-class', 'style' => 'color: red'])
		->and($data['additional_rows'][1]['class'])->toBe('footer-class');
});

it('rejects invalid table data', function (array $data) {
	expect(fn() => (new TableRenderer())->render(['id' => 'table'] + $data))->toThrow(InvalidArgumentException::class);
})->with([
	'headers collection' => [['headers' => 'invalid']],
	'rows collection'    => [['rows' => 'invalid']],
	'header item'        => [['headers' => ['invalid']]],
	'row item'           => [['rows' => ['invalid']]],
	'row without data'   => [['rows' => [[]]]],
	'row cells'          => [['rows' => [['data' => 'invalid']]]],
	'cell item'          => [['rows' => [['data' => ['invalid']]]]],
]);

it('rejects invalid table columns returned by an external builder', function () {
	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn(['columns' => 'invalid']);

	expect(fn() => (new TablePresenter(new TableRenderer()))->show($builder))->toThrow(InvalidArgumentException::class);
});
