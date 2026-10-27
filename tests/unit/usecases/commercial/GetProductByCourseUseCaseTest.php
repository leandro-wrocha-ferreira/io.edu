<?php

namespace tests\unit\usecases\commercial;

use app\domain\commercial\Product;
use app\domain\commercial\value_objects\Price;
use app\domain\course\Category;
use app\domain\course\Course;
use app\usecases\commercial\GetProductByCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCourseRepository;
use tests\unit\mocks\repositories\MockProductRepository;

/**
 * Unit tests for GetProductByCourseUseCase.
 */
class GetProductByCourseUseCaseTest extends TestCase
{
	private MockProductRepository $product_repository;
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private GetProductByCourseUseCase $use_case;
	private Course $course;

	protected function setUp(): void
	{
		parent::setUp();
		$this->product_repository = new MockProductRepository();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new GetProductByCourseUseCase($this->product_repository);

		$category = $this->category_repository->create(Category::create('Design', 'design'));
		$this->course = $this->course_repository->create(Course::create($category->get_id(), 'UI Design', 'ui-design'));
	}

	public function test_get_product_by_course_returns_product_when_exists(): void
	{
		$product = $this->product_repository->save(Product::create(
			$this->course->get_id(),
			false,
			Price::from_cents(5000)
		));

		$result = $this->use_case->execute($this->course->get_id());

		$this->assertNotNull($result);
		$this->assertSame($product->get_id(), $result->get_id());
		$this->assertSame(5000, $result->get_price()->get_cents());
	}

	public function test_get_product_by_course_returns_null_when_not_found(): void
	{
		$result = $this->use_case->execute(9999);
		$this->assertNull($result);
	}
}
