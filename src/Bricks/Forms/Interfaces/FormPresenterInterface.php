<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface FormPresenterInterface
{
	public function show(FormBuilderInterface $builder): void;
}
