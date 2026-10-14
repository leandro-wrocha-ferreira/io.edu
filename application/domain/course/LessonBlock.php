<?php

namespace app\domain\course;

use app\domain\course\blocks\AttachmentBlock;
use app\domain\course\blocks\QuizBlock;
use app\domain\course\blocks\TextBlock;
use app\domain\course\blocks\VideoBlock;
use app\domain\course\constants\BlockType;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Abstract domain entity representing a polymorphic content block within a lesson.
 */
abstract class LessonBlock
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	protected ?int $id = null;

	/**
	 * Parent lesson ID.
	 *
	 * @var int
	 */
	protected int $lesson_id;

	/**
	 * Block type ('video', 'text', 'attachment', 'quiz').
	 *
	 * @var string
	 */
	protected string $type;

	/**
	 * Sequential sort order within the lesson.
	 *
	 * @var int
	 */
	protected int $sort_order = 0;

	/**
	 * Whether completing this block is required to complete the lesson/enrollment.
	 *
	 * @var bool
	 */
	protected bool $is_required = true;

	/**
	 * Creation timestamp.
	 *
	 * @var DateTime|null
	 */
	protected ?DateTime $created_at = null;

	/**
	 * Update timestamp.
	 *
	 * @var DateTime|null
	 */
	protected ?DateTime $updated_at = null;

	/**
	 * Soft deletion timestamp.
	 *
	 * @var DateTime|null
	 */
	protected ?DateTime $deleted_at = null;

	/**
	 * Polymorphic factory method to instantiate the concrete LessonBlock subclass.
	 *
	 * @param int $lesson_id Parent lesson ID
	 * @param string $type Block type ('video', 'text', 'attachment', 'quiz')
	 * @param array<string, mixed>|string $payload Payload array or JSON string
	 * @param int $sort_order Sequential sort order
	 * @param bool $is_required Whether block completion is mandatory
	 * @param int|null $id Block ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return LessonBlock
	 * @throws ValidationException
	 */
	public static function create(
		int $lesson_id,
		string $type,
		array|string $payload = [],
		int $sort_order = 0,
		bool $is_required = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): LessonBlock
	{
		if (!BlockType::is_valid($type)) {
			throw new ValidationException("Invalid block type: {$type}");
		}

		$parsed_payload = is_string($payload) ? (json_decode($payload, true) ?? []) : $payload;
		if (!is_array($parsed_payload)) {
			$parsed_payload = [];
		}

		if ($type === BlockType::VIDEO) {
			return VideoBlock::create_video(
				$lesson_id,
				(string) ($parsed_payload['provider'] ?? 'youtube'),
				(string) ($parsed_payload['url'] ?? ''),
				(int) ($parsed_payload['duration_in_seconds'] ?? 0),
				$sort_order,
				$is_required,
				$id,
				$created_at,
				$updated_at,
				$deleted_at
			);
		}

		if ($type === BlockType::TEXT) {
			$min_reading_time = isset($parsed_payload['min_reading_time_in_minutes'])
				? (int) $parsed_payload['min_reading_time_in_minutes']
				: (isset($parsed_payload['min_reading_time_in_seconds']) ? (int) ceil(((int)$parsed_payload['min_reading_time_in_seconds']) / 60) : 0);

			return TextBlock::create_text(
				$lesson_id,
				(string) ($parsed_payload['content'] ?? ''),
				$min_reading_time,
				$sort_order,
				$is_required,
				$id,
				$created_at,
				$updated_at,
				$deleted_at
			);
		}

		if ($type === BlockType::ATTACHMENT) {
			$min_reading_time = isset($parsed_payload['min_reading_time_in_minutes'])
				? (int) $parsed_payload['min_reading_time_in_minutes']
				: (isset($parsed_payload['min_reading_time_in_seconds']) ? (int) ceil(((int)$parsed_payload['min_reading_time_in_seconds']) / 60) : 0);

			return AttachmentBlock::create_attachment(
				$lesson_id,
				(string) ($parsed_payload['filename'] ?? ''),
				(string) ($parsed_payload['filepath'] ?? ''),
				$min_reading_time,
				(int) ($parsed_payload['file_size'] ?? 0),
				(string) ($parsed_payload['file_type'] ?? ''),
				$sort_order,
				$is_required,
				$id,
				$created_at,
				$updated_at,
				$deleted_at
			);
		}

		if ($type === BlockType::QUIZ) {
			$question = (string) ($parsed_payload['question'] ?? '');
			$explanation = (string) ($parsed_payload['explanation'] ?? '');
			$questions = isset($parsed_payload['questions']) && is_array($parsed_payload['questions']) ? $parsed_payload['questions'] : [];
			$min_score = (int) ($parsed_payload['min_score_percentage'] ?? 0);

			if ($question === '' && !empty($questions) && isset($questions[0]['text'])) {
				$question = (string) $questions[0]['text'];
			}

			return QuizBlock::create_quiz(
				$lesson_id,
				$question,
				$explanation,
				$questions,
				$min_score,
				$sort_order,
				$is_required,
				$id,
				$created_at,
				$updated_at,
				$deleted_at
			);
		}

		throw new ValidationException("Unsupported block type: {$type}");
	}

	/**
	 * Change block sort order.
	 *
	 * @param int $new_order
	 * @return void
	 */
	public function change_sort_order(int $new_order): void
	{
		$this->sort_order = max(0, $new_order);
		$this->updated_at = new DateTime();
	}

	/**
	 * Mark block as deleted.
	 *
	 * @return void
	 */
	public function delete(): void
	{
		$this->deleted_at = new DateTime();
		$this->updated_at = new DateTime();
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_lesson_id(): int
	{
		return $this->lesson_id;
	}

	public function get_type(): string
	{
		return $this->type;
	}

	/**
	 * Abstract method to extract the block-specific payload array.
	 *
	 * @return array<string, mixed>
	 */
	abstract public function get_payload(): array;

	/**
	 * Get payload encoded as JSON string.
	 *
	 * @return string
	 */
	public function get_payload_json(): string
	{
		return json_encode($this->get_payload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
	}

	public function get_sort_order(): int
	{
		return $this->sort_order;
	}

	public function is_required(): bool
	{
		return $this->is_required;
	}

	public function is_deleted(): bool
	{
		return $this->deleted_at !== null;
	}

	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}

	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}
}
