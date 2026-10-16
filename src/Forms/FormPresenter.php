<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\DisplaysOutput;
use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\Forms\Interfaces\FormPresenterInterface;
use Bugo\Bricks\RendererInterface;
use Bugo\Bricks\StringRendererInterface;

readonly class FormPresenter implements FormPresenterInterface
{
	use DisplaysOutput;

	public function __construct(private RendererInterface|StringRendererInterface $renderer) {}

	public function show(FormBuilderInterface $builder): void
	{
		$this->display($builder->build());
	}
}
