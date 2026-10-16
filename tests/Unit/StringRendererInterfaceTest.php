<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbPresenter;
use Bugo\Bricks\Breadcrumbs\BreadcrumbRenderer;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use Bugo\Bricks\Forms\FormPresenter;
use Bugo\Bricks\Forms\FormRenderer;
use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\RendererInterface;
use Bugo\Bricks\StringRendererInterface;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Bricks\Tables\TablePresenter;
use Bugo\Bricks\Tables\TableRenderer;

it('returns markup without writing to the output', function (string $class, array $data, string $expected) {
	$renderer = new $class();

	ob_start();
	try {
		$html = $renderer->render($data);
		$output = ob_get_contents();
	} finally {
		ob_end_clean();
	}

	expect($renderer)->toBeInstanceOf(StringRendererInterface::class)
		->and($html)->toBeString()->toContain($expected)
		->and($output)->toBe('');
})->with([
	'breadcrumbs' => [BreadcrumbRenderer::class, [['name' => 'Home']], '<a href="#">Home</a>'],
	'forms'       => [FormRenderer::class, ['id' => 'form_id', 'title' => 'Form', 'script' => 'initForm();'], '<script>initForm();</script>'],
	'tables'      => [TableRenderer::class, ['id' => 'table_id', 'javascript' => 'initTable();'], '<script>initTable();</script>'],
]);

it('keeps presenters compatible with output renderers', function (string $presenterClass, string $builderInterface) {
	$data = ['id' => 'example', 'columns' => []];

	$builder = mock($builderInterface);
	$builder->shouldReceive('build')->once()->andReturn($data);

	$renderer = new class implements RendererInterface {
		public array $data = [];
		public int $calls = 0;

		public function render(array $data): void
		{
			$this->data = $data;
			$this->calls++;

			echo 'Legacy output';
		}
	};

	ob_start();
	try {
		(new $presenterClass($renderer))->show($builder);
		$output = ob_get_contents();
	} finally {
		ob_end_clean();
	}

	expect($output)->toBe('Legacy output')
		->and($renderer->calls)->toBe(1)
		->and($renderer->data)->toBe($data);
})->with([
	'breadcrumbs' => [BreadcrumbPresenter::class, BreadcrumbBuilderInterface::class],
	'forms'       => [FormPresenter::class, FormBuilderInterface::class],
	'tables'      => [TablePresenter::class, TableBuilderInterface::class],
]);
