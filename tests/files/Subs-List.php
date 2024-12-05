<?php declare(strict_types=1);

use Nette\Utils\Html;

if (! function_exists('createList')) {
	function createList(array $listOptions): void
	{
		$root = Html::el();
		$form = Html::el('form');

		if (isset($listOptions['title'])) {
			$div = Html::el('div')->class('cat_bar');
			$h3 = Html::el('h3')->class('cat' . 'bg')->setHtml($listOptions['title']);
			$div->addHtml($h3);
			$form->addHtml($div);
		}

		$table = Html::el('table')->id($listOptions['id']);
		$tr = Html::el('tr');

		if (! empty($listOptions['headers'])) {
			foreach ($listOptions['headers'] as $header) {
				$th = Html::el('th');
				$th->id($header['id']);
				$th->class($header['class'] ?? null);
				$th->style($header['style'] ?? null);
				$th->setHtml($header['label']);

				$tr->addHtml($th);
			}
		}

		$table->addHtml($tr);

		if (! empty($listOptions['rows'])) {
			foreach ($listOptions['rows'] as $row) {
				$data = $row['data'];
				$tr = Html::el('tr');
				$tr->class('window' . 'bg');

				foreach ($data as $cell) {
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

		if (! empty($listOptions['javascript'])) {
			$script = Html::el('script')->setText($listOptions['javascript']);
			$root->addHtml($script);
		}

		echo $root->toHtml();
	}
}
