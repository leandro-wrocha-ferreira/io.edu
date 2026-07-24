<?php

namespace app\Factories;

class Model_factory
{
    public static function make(string $name)
    {
        $CI =& get_instance();
        $CI->load->model($name);
        return $CI->{$name};
    }
}
