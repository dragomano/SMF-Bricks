<?php declare(strict_types=1);

namespace Tests\Unit\Settings;

use Bugo\Bricks\Settings\AbstractConfig;

class SomeConfig extends AbstractConfig {
	protected string $type = 'some_type';
};
