<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\RendererInterface;
use Nette\Utils\Html;

class TableRenderer implements RendererInterface
{
	public function render(array $data): void
	{
		$root = Html::el();
		$form = Html::el('form');

		if (isset($data['title'])) {
			$div = Html::el('div')->class('cat_bar');
			$h3 = Html::el('h3')->class('cat' . 'bg')->setHtml($data['title']);
			$div->addHtml($h3);
			$form->addHtml($div);
		}

		$table = Html::el('table')->id($data['id']);
		$tr = Html::el('tr');

		if (! empty($data['headers'])) {
			foreach ($data['headers'] as $header) {
				$th = Html::el('th');
				$th->id($header['id']);
				$th->class($header['class'] ?? null);
				$th->style($header['style'] ?? null);
				$th->setHtml($header['label']);

				$tr->addHtml($th);
			}
		}

		$table->addHtml($tr);

		if (! empty($data['rows'])) {
			foreach ($data['rows'] as $row) {
				$rowData = $row['data'];
				$tr = Html::el('tr');
				$tr->class('window' . 'bg');

				foreach ($rowData as $cell) {
					$td = Html::el('td');
					$td->class($cell['class'] ?? null);
					$td->style($cell['style'] ?? null);
					$td->setHtml($cell['value']);

					$tr->addHtml($td);
				}

				$table->addHtml($tr);
			}
		}

		$form->addHtml($table);
		$root->addHtml($form);

		if (! empty($data['javascript'])) {
			$script = Html::el('script')->setText($data['javascript']);
			$root->addHtml($script);
		}

		echo $root->toHtml();
	}
}
