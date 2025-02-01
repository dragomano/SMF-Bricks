<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\Renderers\RendererInterface;

class FormPresenter implements FormPresenterInterface
{
	public function __construct(private readonly RendererInterface $renderer) {}

	public function show(FormBuilderInterface $builder): void
	{
		$data = $builder->build();

		$this->renderer->render($data);
	}
}
