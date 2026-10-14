<?php

namespace app\domain\course\constants;

/**
 * Constants representing lesson block content types.
 */
final class BlockType
{
	public const VIDEO = 'video';
	public const TEXT = 'text';
	public const ATTACHMENT = 'attachment';
	public const QUIZ = 'quiz';

	public const ALL = [
		self::VIDEO,
		self::TEXT,
		self::ATTACHMENT,
		self::QUIZ,
	];

	/**
	 * Check if a given block type string is valid.
	 *
	 * @param string $type
	 * @return bool
	 */
	public static function is_valid(string $type): bool
	{
		return in_array($type, self::ALL, true);
	}
}
