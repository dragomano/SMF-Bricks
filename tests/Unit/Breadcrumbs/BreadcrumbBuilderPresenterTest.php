<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbItem;
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
