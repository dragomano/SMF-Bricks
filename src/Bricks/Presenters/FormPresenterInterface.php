<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;

interface FormPresenterInterface
{
	public function show(FormBuilderInterface $builder): void;
}
