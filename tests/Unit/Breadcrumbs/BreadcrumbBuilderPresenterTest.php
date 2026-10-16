<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbItem;
use Bugo\Bricks\Breadcrumbs\BreadcrumbBuilder;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use Bugo\Bricks\Breadcrumbs\BreadcrumbPresenter;
use Bugo\Bricks\Breadcrumbs\BreadcrumbRenderer;

it('renders breadcrumbs correctly', function () {
	$data = [
		BreadcrumbItem::make('Home', '/')
			->setBefore('🏠'),
		BreadcrumbItem::make('About', '/about'),
		BreadcrumbItem::make('Contacts', '/contacts')
			->setAfter('✉️'),
	];

	$builder = mock(BreadcrumbBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn($data);

	ob_start();
	$renderer = new BreadcrumbRenderer();
	$presenter = new BreadcrumbPresenter($renderer);
	$presenter->show($builder);
	$output = ob_get_clean();

	expect($output)->toContain('<nav aria-label="breadcrumbs">')
		->and($output)->toContain('<ul class="breadcrumbs">')
		->and($output)->toContain('<li class="breadcrumb-item">🏠<a href="/">Home</a></li>')
		->and($output)->toContain('<li class="breadcrumb-item"><a href="/about">About</a></li>')
		->and($output)->toContain('<li class="breadcrumb-item"><a href="/contacts">Contacts</a>✉️</li>')
		->and($output)->toContain('</ul></nav>');
});

it('renders the arrays returned by a real breadcrumb builder', function () {
	$builder = BreadcrumbBuilder::make()
		->add('', '/empty')
		->add('Home & news', '/?a=1&b=2', '<b>Before</b>', '<i>After</i>');

	ob_start();
	try {
		(new BreadcrumbPresenter(new BreadcrumbRenderer()))->show($builder);
		$output = ob_get_contents();
	} finally {
		ob_end_clean();
	}

	expect($output)->toContain('<li class="breadcrumb-item"><b>Before</b><a href="/?a=1&amp;b=2">Home &amp; news</a><i>After</i></li>')
		->and($output)->not->toContain('/empty');
});

it('renders zero strings returned by the builder', function () {
	$data = BreadcrumbBuilder::make()->add('0', '0', '0', '0')->build();

	expect((new BreadcrumbRenderer())->render($data))
		->toContain('<li class="breadcrumb-item">0<a href="0">0</a>0</li>');
});

it('skips invalid breadcrumb entries', function () {
	$html = (new BreadcrumbRenderer())->render(['', null, [], ['url' => '/missing-name'], ['name' => 'Home']]);

	expect($html)->toBe('<nav aria-label="breadcrumbs"><ul class="breadcrumbs"><li class="breadcrumb-item"><a href="#">Home</a></li></ul></nav>');
});
