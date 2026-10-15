<?php

declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use function in_array;

enum HtmlFieldType: string
{
	case BUTTON   = 'button';
	case CHECKBOX = 'checkbox';
	case COLOR    = 'color';
	case DATE     = 'date';
	case EMAIL    = 'email';
	case FILE     = 'file';
	case HIDDEN   = 'hidden';
	case NUMBER   = 'number';
	case PASSWORD = 'password';
	case RADIO    = 'radio';
	case RANGE    = 'range';
	case RESET    = 'reset';
	case SEARCH   = 'search';
	case SELECT   = 'select';
	case SUBMIT   = 'submit';
	case TEL      = 'tel';
	case TEXT     = 'text';
	case TEXTAREA = 'textarea';
	case URL      = 'url';

	public function isInput(): bool
	{
		return ! in_array($this, [
			self::BUTTON,
			self::RESET,
			self::SELECT,
			self::SUBMIT,
			self::TEXTAREA,
		], true);
	}

	public function getTagName(): string
	{
		return $this->isInput() ? 'input' : $this->value;
	}
}
