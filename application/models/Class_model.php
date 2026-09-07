<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\education\CourseClass;
use app\domain\education\ClassRepositoryInterface;

/**
 * Class model implementing ClassRepositoryInterface.
 */
class Class_model extends MY_Model implements ClassRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'classes';

	/**
	 * Entity class for automatic hydration.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = CourseClass::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a class by ID.
	 *
	 * @param int|string $id Class ID
	 * @return CourseClass|null
	 */
	public function find_by_id($id): ?CourseClass
	{
		return parent::find_by_id($id);
	}

	/**
	 * Find all classes belonging to a course.
	 *
	 * @param int $course_id
	 * @return CourseClass[]
	 */
	public function find_by_course(int $course_id): array
	{
		$rows = $this->db
			->from($this->table)
			->where('course_id', $course_id)
			->order_by('created_at', 'DESC')
			->get()
			->result_array();

		return $this->to_entities($rows);
	}

	/**
	 * Save a CourseClass entity (insert or update).
	 *
	 * @param CourseClass $class
	 * @return int
	 */
	public function save(CourseClass $class): int
	{
		$data = [
			'course_id'              => $class->get_course_id(),
			'name'                   => $class->get_name(),
			'modality'               => $class->get_modality(),
			'price'                  => $class->get_price(),
			'total_duration_seconds' => $class->get_total_duration_seconds(),
		];

		if ($class->get_id() !== null) {
			$this->update($data, ['id' => $class->get_id()]);
			return $class->get_id();
		}

		$id = (int) $this->insert($data);
		$class->set_id($id);
		return $id;
	}


	/**
	 * Associate specific lessons with a class and recalculate total duration in seconds.
	 *
	 * @param int $class_id
	 * @param array $lesson_ids
	 * @return int Total duration in seconds
	 */
	public function sync_lessons(int $class_id, array $lesson_ids): int
	{
		// 1. Clear previous lesson links for this class
		$this->db->where('class_id', $class_id)->delete('class_lessons');

		$total_duration = 0;

		// 2. Insert new links and calculate total duration
		if (!empty($lesson_ids)) {
			$batch = [];
			foreach ($lesson_ids as $lid) {
				$batch[] = [
					'class_id'  => $class_id,
					'lesson_id' => (int) $lid,
				];
			}
			$this->db->insert_batch('class_lessons', $batch);

			// Calculate sum of duration_seconds from lessons table
			$row = $this->db
				->select_sum('duration_seconds', 'total_duration')
				->from('lessons')
				->where_in('id', array_map('intval', $lesson_ids))
				->get()
				->row_array();

			$total_duration = (int) ($row['total_duration'] ?? 0);
		}

		// 3. Update total_duration_seconds on the class record
		$this->update(['total_duration_seconds' => $total_duration], ['id' => $class_id]);

		return $total_duration;
	}
}
