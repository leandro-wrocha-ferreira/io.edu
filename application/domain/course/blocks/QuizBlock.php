<?php

namespace app\domain\course\blocks;

use app\domain\course\constants\BlockType;
use app\domain\course\LessonBlock;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a Quiz / Learning Check block.
 */
class QuizBlock extends LessonBlock
{
	/**
	 * Question prompt.
	 *
	 * @var string
	 */
	private string $question;

	/**
	 * Explanatory pedagogical feedback.
	 *
	 * @var string
	 */
	private string $explanation = '';

	/**
	 * List of questions (for multi-question quizzes).
	 *
	 * @var array<array<string, mixed>>
	 */
	private array $questions = [];

	/**
	 * Minimum score percentage required to pass (0 to 100).
	 *
	 * @var int
	 */
	private int $min_score_percentage = 0;

	/**
	 * Factory method for QuizBlock.
	 *
	 * @param int $lesson_id Parent lesson ID
	 * @param string $question Question prompt
	 * @param string $explanation Feedback text
	 * @param array<array<string, mixed>> $questions Optional questions collection
	 * @param int $min_score_percentage Minimum pass score percentage
	 * @param int $sort_order Sequential sort order
	 * @param bool $is_required Mandatory completion flag
	 * @param int|null $id Block ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 */
	public static function create_quiz(
		int $lesson_id,
		string $question,
		string $explanation = '',
		array $questions = [],
		int $min_score_percentage = 0,
		int $sort_order = 0,
		bool $is_required = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$trimmed_question = trim($question);
		if ($trimmed_question === '' && empty($questions)) {
			throw new ValidationException("Quiz question prompt cannot be empty");
		}

		$block = new self();
		$block->id = $id;
		$block->lesson_id = $lesson_id;
		$block->type = BlockType::QUIZ;
		$block->question = $trimmed_question;
		$block->explanation = trim($explanation);
		$block->questions = $questions;
		$block->min_score_percentage = max(0, min(100, $min_score_percentage));
		$block->sort_order = max(0, $sort_order);
		$block->is_required = $is_required;
		$block->created_at = $created_at ?? new DateTime();
		$block->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$block->deleted_at = $deleted_at;

		return $block;
	}

	/**
	 * Update quiz block parameters.
	 *
	 * @param string $question
	 * @param string $explanation
	 * @param array<array<string, mixed>> $questions
	 * @param int $min_score_percentage
	 * @param bool $is_required
	 * @return void
	 * @throws ValidationException When question prompt is empty
	 */
	public function update_quiz(
		string $question,
		string $explanation = '',
		array $questions = [],
		int $min_score_percentage = 0,
		bool $is_required = true
	): void
	{
		$trimmed_question = trim($question);
		if ($trimmed_question === '' && empty($questions)) {
			throw new ValidationException("Quiz question prompt cannot be empty");
		}

		$this->question = $trimmed_question;
		$this->explanation = trim($explanation);
		$this->questions = $questions;
		$this->min_score_percentage = max(0, min(100, $min_score_percentage));
		$this->is_required = $is_required;
		$this->updated_at = new DateTime();
	}

	public function get_question(): string
	{
		return $this->question;
	}

	public function get_explanation(): string
	{
		return $this->explanation;
	}

	/**
	 * @return array<array<string, mixed>>
	 */
	public function get_questions(): array
	{
		return $this->questions;
	}

	public function get_min_score_percentage(): int
	{
		return $this->min_score_percentage;
	}

	/**
	 * Get serializable array payload.
	 *
	 * @return array<string, mixed>
	 */
	public function get_payload(): array
	{
		return [
			'question' => $this->question,
			'explanation' => $this->explanation,
			'questions' => $this->questions,
			'min_score_percentage' => $this->min_score_percentage,
		];
	}
}
