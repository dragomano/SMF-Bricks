<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\Forms\Interfaces\FormPresenterInterface;
use Bugo\Bricks\RendererInterface;

readonly class FormPresenter implements FormPresenterInterface
{
	public function __construct(private RendererInterface $renderer) {}

	public function show(FormBuilderInterface $builder): void
	{
		$data = $builder->build();

		$this->renderer->render($data);
	}
}
