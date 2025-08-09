<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\RendererInterface;
use Nette\Utils\Html;

class BreadcrumbRenderer implements RendererInterface
{
	public function render(array $data): void
	{
		$nav = Html::el('nav', ['aria-label' => 'breadcrumbs']);
		$ul  = Html::el('ul', ['class' => 'breadcrumbs']);

		foreach ($data as $item) {
			$li = Html::el('li', ['class' => 'breadcrumb-item']);
			$li->addHtml(Html::el('a', ['href' => $item->getUrl() ?? '#'])->setText($item->getName()));

			if ($item->getBefore()) {
				$li->setHtml($item->getBefore() . $li->getHtml());
			}

			if ($item->getAfter()) {
				$li->setHtml($li->getHtml() . $item->getAfter());
			}

			$ul->addHtml($li);
		}

		$nav->addHtml($ul);

		echo $nav->toHtml();
	}
}
