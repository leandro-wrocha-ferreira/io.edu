<?php

namespace app\models\mappers;

use app\domain\course\blocks\AttachmentBlock;
use app\domain\course\blocks\QuizBlock;
use app\domain\course\blocks\TextBlock;
use app\domain\course\blocks\VideoBlock;
use app\domain\course\constants\BlockType;
use app\domain\course\LessonBlock;
use app\models\dtos\LessonBlockDatabase;

/**
 * Mapper for converting between raw database data and LessonBlock polymorphic domain entities.
 */
class LessonBlockMapper
{
	/**
	 * Convert a raw database row DTO into a LessonBlock polymorphic domain entity.
	 *
	 * @param LessonBlockDatabase $row Raw database row DTO
	 * @return LessonBlock LessonBlock domain entity
	 */
	public static function to_entity(LessonBlockDatabase $row): LessonBlock
	{
		if ($row->type === BlockType::VIDEO) {
			return VideoBlock::create_video(
				$row->lesson_id,
				$row->provider ?? 'youtube',
				$row->url ?? '',
				$row->duration_in_seconds ?? 0,
				$row->sort_order,
				(bool) $row->is_required,
				$row->id,
				$row->created_at,
				$row->updated_at,
				$row->deleted_at
			);
		}

		if ($row->type === BlockType::TEXT) {
			return TextBlock::create_text(
				$row->lesson_id,
				$row->content ?? '',
				$row->min_reading_time_in_minutes ?? 0,
				$row->sort_order,
				(bool) $row->is_required,
				$row->id,
				$row->created_at,
				$row->updated_at,
				$row->deleted_at
			);
		}

		if ($row->type === BlockType::ATTACHMENT) {
			return AttachmentBlock::create_attachment(
				$row->lesson_id,
				$row->filename ?? '',
				$row->filepath ?? '',
				$row->min_reading_time_in_minutes ?? 0,
				$row->file_size ?? 0,
				$row->file_type ?? '',
				$row->sort_order,
				(bool) $row->is_required,
				$row->id,
				$row->created_at,
				$row->updated_at,
				$row->deleted_at
			);
		}

		// Fallback for QuizBlock or future types
		return QuizBlock::create_quiz(
			$row->lesson_id,
			'Quiz',
			'',
			[],
			0,
			$row->sort_order,
			(bool) $row->is_required,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of LessonBlock domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<LessonBlock>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new LessonBlockDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a LessonBlock entity to the master lesson_blocks table array for insertion.
	 *
	 * @param LessonBlock $block
	 * @return array<string, mixed>
	 */
	public static function to_database_create(LessonBlock $block): array
	{
		return [
			'lesson_id' => $block->get_lesson_id(),
			'type' => $block->get_type(),
			'sort_order' => $block->get_sort_order(),
			'is_required' => $block->is_required() ? 1 : 0,
		];
	}

	/**
	 * Map a LessonBlock entity to the master lesson_blocks table array for update.
	 *
	 * @param LessonBlock $block
	 * @return array<string, mixed>
	 */
	public static function to_database_update(LessonBlock $block): array
	{
		return [
			'lesson_id' => $block->get_lesson_id(),
			'type' => $block->get_type(),
			'sort_order' => $block->get_sort_order(),
			'is_required' => $block->is_required() ? 1 : 0,
			'deleted_at' => $block->get_deleted_at() ? $block->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}

	/**
	 * Map a LessonBlock concrete subtype to its respective child table name and payload.
	 *
	 * @param LessonBlock $block
	 * @param int $block_id
	 * @return array{table: string, data: array<string, mixed>}|null
	 */
	public static function to_child_database_data(LessonBlock $block, int $block_id): ?array
	{
		if ($block instanceof VideoBlock) {
			return [
				'table' => 'video_blocks',
				'data' => [
					'block_id' => $block_id,
					'provider' => $block->get_provider(),
					'url' => $block->get_url(),
					'duration_in_seconds' => $block->get_duration_in_seconds(),
				],
			];
		}

		if ($block instanceof TextBlock) {
			return [
				'table' => 'text_blocks',
				'data' => [
					'block_id' => $block_id,
					'content' => $block->get_content(),
					'min_reading_time_in_minutes' => $block->get_min_reading_time_in_minutes(),
				],
			];
		}

		if ($block instanceof AttachmentBlock) {
			return [
				'table' => 'attachment_blocks',
				'data' => [
					'block_id' => $block_id,
					'filename' => $block->get_filename(),
					'filepath' => $block->get_filepath(),
					'file_size' => $block->get_file_size(),
					'file_type' => $block->get_file_type(),
					'min_reading_time_in_minutes' => $block->get_min_reading_time_in_minutes(),
				],
			];
		}

		return null;
	}
}
