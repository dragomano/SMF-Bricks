<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbPresenterInterface;
use Bugo\Bricks\DisplaysOutput;
use Bugo\Bricks\RendererInterface;
use Bugo\Bricks\StringRendererInterface;

readonly class BreadcrumbPresenter implements BreadcrumbPresenterInterface
{
	use DisplaysOutput;

	public function __construct(private RendererInterface|StringRendererInterface $renderer) {}

	public function show(BreadcrumbBuilderInterface $builder): void
	{
		$this->display($builder->build());
	}
}
