<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskPriority: string
{
	case LOW = 'low';
	case NORMAL = 'normal';
	case URGENT = 'urgent';

	public function label(): string
	{
		return match ($this) {
			self::LOW => 'Low',
			self::NORMAL => 'Normal',
			self::URGENT => 'Urgent',
		};
	}
}


