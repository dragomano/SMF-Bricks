<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbBuilder;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbItemInterface;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\Interfaces\ButtonInterface;
use Bugo\Bricks\Forms\Interfaces\FieldInterface;
use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\Settings\ConfigBuilder;
use Bugo\Bricks\Settings\Interfaces\ConfigBuilderInterface;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;
use Bugo\Bricks\Tables\Interfaces\ColumnInterface;
use Bugo\Bricks\Tables\Interfaces\RowInterface;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Bricks\Tables\TableBuilder;

it('accepts independent item implementations through builder contracts', function (
	string $builderInterface,
	Closure $makeBuilder,
	string $method,
	string $itemInterface,
	array $path,
	bool $storesObject = false
) {
	$parameter = (new ReflectionMethod($builderInterface, $method))->getParameters()[0];

	$item = mock($itemInterface);
	$item->shouldReceive('getName')->andReturn('custom');
	$item->shouldReceive('toArray')->andReturn(['name' => 'custom']);

	$builder = $makeBuilder();

	expect((string) $parameter->getType())->toBe($itemInterface)
		->and($builder)->toBeInstanceOf($builderInterface)
		->and($builder->{$method}($item))->toBe($builder);

	$actual = $builder->build();
	foreach ($path as $key) {
		$actual = $actual[$key];
	}

	expect($actual)->toBe($storesObject ? $item : ['name' => 'custom']);
})->with([
	'fields'      => [FormBuilderInterface::class, fn() => FormBuilder::make('form'), 'addField', FieldInterface::class, ['fields', 'custom'], true],
	'buttons'     => [FormBuilderInterface::class, fn() => FormBuilder::make('form'), 'addButton', ButtonInterface::class, ['buttons', 'custom'], true],
	'columns'     => [TableBuilderInterface::class, fn() => TableBuilder::make('table', 'Table'), 'addColumn', ColumnInterface::class, ['columns', 'custom']],
	'rows'        => [TableBuilderInterface::class, fn() => TableBuilder::make('table', 'Table'), 'addRow', RowInterface::class, ['additional_rows', 0]],
	'breadcrumbs' => [BreadcrumbBuilderInterface::class, fn() => BreadcrumbBuilder::make(), 'addItem', BreadcrumbItemInterface::class, [0]],
	'config'      => [ConfigBuilderInterface::class, fn() => ConfigBuilder::make(), 'addVar', ConfigInterface::class, [0]],
]);
