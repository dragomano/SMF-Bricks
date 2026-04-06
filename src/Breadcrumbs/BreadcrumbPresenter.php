<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbPresenterInterface;
use Bugo\Bricks\RendererInterface;

readonly class BreadcrumbPresenter implements BreadcrumbPresenterInterface
{
	public function __construct(private RendererInterface $renderer) {}

	public function show(BreadcrumbBuilderInterface $builder): void
	{
		$this->renderer->render($builder->build());
	}
}
