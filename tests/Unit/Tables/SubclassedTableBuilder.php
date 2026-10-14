<?php declare(strict_types=1);

namespace Tests\Unit\Tables;

use Bugo\Bricks\Tables\TableBuilder;

/**
 * Mimics the way Light Portal extends the builder:
 * protected constructor calling parent::__construct() and parent::getTitle().
 */
class SubclassedTableBuilder extends TableBuilder
{
	protected function __construct(string $id, string $title)
	{
		parent::__construct($id, $title);

		$this->paginate(20);
		$this->setNoItemsLabel('There is no data yet');
		$this->setFormAction('https://example.com/index.php?action=admin');
	}

	public function withTitleSuffix(string $suffix): static
	{
		return $this->setTitle(parent::getTitle() . $suffix);
	}
}
