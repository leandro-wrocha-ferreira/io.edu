<?php

namespace app\factories;

/**
 * Factory for loading CI3 models.
 *
 * Provides a clean way for use cases to load models
 * without directly coupling to CI3's get_instance().
 */
class Model_factory
{
    /**
     * Load and return a CI3 model instance.
     *
     * @param string $name Model name (e.g. 'User_model')
     * @return mixed The loaded model instance
     */
    public static function make(string $name)
    {
        $CI =& get_instance();
        $CI->load->model($name);
        return $CI->{$name};
    }
}
