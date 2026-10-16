<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\StringRendererInterface;
use Nette\Utils\Html;
use Stringable;

use function is_scalar;

class TableRenderer implements StringRendererInterface
{
	/**
	 * When enabled, the values taken from the data array are escaped instead of being
	 * inserted as raw HTML. Disabled by default to keep the SMF markup working.
	 */
	public function __construct(private readonly bool $escapeData = false) {}

	/**
	 * @param array<int|string, mixed> $data
	 */
	public function render(array $data): string
	{
		$root = Html::el();
		$form = Html::el('form');

		if (isset($data['title'])) {
			$div = Html::el('div')->class('cat_bar');
			$h3  = Html::el('h3')->class('cat' . 'bg');
			$form->addHtml($div->addHtml($this->fill($h3, $data['title'])));
		}

		$table = Html::el('table')->id($data['id']);
		$tr    = Html::el('tr');

		if (! empty($data['headers'])) {
			foreach ($data['headers'] as $header) {
				$th = Html::el('th');
				$th->id($header['id']);
				$th->class($header['class'] ?? null);
				$th->style($header['style'] ?? null);

				$tr->addHtml($this->fill($th, $header['label']));
			}
		}

		$table->addHtml($tr);

		if (! empty($data['rows'])) {
			foreach ($data['rows'] as $row) {
				$rowData = $row['data'];
				$tr      = Html::el('tr');
				$tr->class('window' . 'bg');

				foreach ($rowData as $cell) {
					$td = Html::el('td');
					$td->class($cell['class'] ?? null);
					$td->style($cell['style'] ?? null);

					$tr->addHtml($this->fill($td, $cell['value'] ?? ''));
				}

				$table->addHtml($tr);
			}
		}

		$form->addHtml($table);
		$root->addHtml($form);

		if (! empty($data['javascript'])) {
			$root->addHtml(Html::el('script')->setText($data['javascript']));
		}

		return (string) $root;
	}

	private function fill(Html $element, mixed $value): Html
	{
		$text = is_scalar($value) || $value instanceof Stringable ? (string) $value : '';

		return $this->escapeData ? $element->setText($text) : $element->setHtml($text);
	}
}
