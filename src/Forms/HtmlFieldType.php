<?php

declare(strict_types=1);

namespace Bugo\Bricks\Forms;

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

	public static function getInputTypes(): array
	{
		return [
			self::CHECKBOX->value,
			self::COLOR->value,
			self::DATE->value,
			self::EMAIL->value,
			self::FILE->value,
			self::NUMBER->value,
			self::PASSWORD->value,
			self::RADIO->value,
			self::RANGE->value,
			self::SEARCH->value,
			self::TEL->value,
			self::TEXT->value,
			self::URL->value,
		];
	}
}
