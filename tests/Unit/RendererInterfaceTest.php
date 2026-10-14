<?php declare(strict_types=1);

use Bugo\Bricks\RendererInterface;

/**
 * Light Portal implements this interface in its own renderers
 * (UI\Tables\TableRenderer and UI\Breadcrumbs\BreadcrumbRenderer),
 * so adding a method here would break every implementor.
 */
it('keeps RendererInterface thin for external implementors', function () {
	$methods = array_map(
		static fn(ReflectionMethod $method) => $method->getName(),
		(new ReflectionClass(RendererInterface::class))->getMethods()
	);

	expect($methods)->toBe(['render']);
});

it('keeps the render() signature stable', function () {
	$method = new ReflectionMethod(RendererInterface::class, 'render');
	$params = $method->getParameters();

	expect($method->getReturnType()?->getName())->toBe('void')
		->and($method->getNumberOfParameters())->toBe(1)
		->and((string) $params[0]->getType())->toBe('array');
});
