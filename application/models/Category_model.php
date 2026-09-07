<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category Model for course categorization.
 */
class Category_model extends MY_Model
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'categories';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}
}
