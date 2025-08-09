<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs\Interfaces;

interface BreadcrumbPresenterInterface
{
	public function show(BreadcrumbBuilderInterface $builder): void;
}
