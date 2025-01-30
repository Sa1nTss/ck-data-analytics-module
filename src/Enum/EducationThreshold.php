<?php

namespace App\Enum;

enum EducationThreshold: string
{
    case MINIMUM = '0.32';
    case BASIC = '0.54';
    case ADVANCED = '0.79';
    case EXPERT = '1';

    public function asFloat(): float
    {
        return (float) $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::MINIMUM => 'Минимальный исходный',
            self::BASIC => 'Базовый',
            self::ADVANCED => 'Продвинутый',
            self::EXPERT => 'Экспертный',
        };
    }
}
