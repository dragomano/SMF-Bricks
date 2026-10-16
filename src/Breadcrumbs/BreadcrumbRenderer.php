<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbItemInterface;
use Bugo\Bricks\StringRendererInterface;
use Nette\Utils\Html;

use function is_array;

class BreadcrumbRenderer implements StringRendererInterface
{
	/**
	 * @param array<int|string, mixed> $data
	 */
	public function render(array $data): string
	{
		$nav = Html::el('nav', ['aria-label' => 'breadcrumbs']);
		$ul  = Html::el('ul', ['class' => 'breadcrumbs']);

		foreach ($data as $item) {
			if ($item instanceof BreadcrumbItemInterface) {
				$item = $item->toArray();
			}

			if (! is_array($item) || ! isset($item['name'])) {
				continue;
			}

			$li = Html::el('li', ['class' => 'breadcrumb-item']);
			$li->addHtml(Html::el('a', ['href' => $item['url'] ?? '#'])->setText($item['name']));

			if (isset($item['before'])) {
				$li->setHtml($item['before'] . $li->getHtml());
			}

			if (isset($item['after'])) {
				$li->setHtml($li->getHtml() . $item['after']);
			}

			$ul->addHtml($li);
		}

		$nav->addHtml($ul);

		return (string) $nav;
	}
}
