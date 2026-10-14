<?php

namespace tests\unit\domain\course\blocks;

use app\domain\course\blocks\QuizBlock;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for QuizBlock domain entity.
 */
class QuizBlockTest extends TestCase
{
	public function test_create_quiz_block_success(): void
	{
		$block = QuizBlock::create_quiz(
			1,
			'Qual o objetivo do DDD?',
			'Modelar o domínio de forma rica',
			[['text' => 'Opção A', 'is_required' => true]],
			80,
			4,
			true
		);

		$this->assertSame('quiz', $block->get_type());
		$this->assertSame('Qual o objetivo do DDD?', $block->get_question());
		$this->assertSame('Modelar o domínio de forma rica', $block->get_explanation());
		$this->assertSame(80, $block->get_min_score_percentage());
		$this->assertTrue($block->is_required());
		$this->assertSame([
			'question' => 'Qual o objetivo do DDD?',
			'explanation' => 'Modelar o domínio de forma rica',
			'questions' => [['text' => 'Opção A', 'is_required' => true]],
			'min_score_percentage' => 80,
		], $block->get_payload());
	}

	public function test_update_quiz_block(): void
	{
		$block = QuizBlock::create_quiz(1, 'Pergunta inicial');
		$block->update_quiz('Pergunta editada', 'Feedback novo', [], 70, false);

		$this->assertSame('Pergunta editada', $block->get_question());
		$this->assertSame('Feedback novo', $block->get_explanation());
		$this->assertSame(70, $block->get_min_score_percentage());
		$this->assertFalse($block->is_required());
	}

	public function test_empty_question_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		QuizBlock::create_quiz(1, '   ', '', []);
	}
}
