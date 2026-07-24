<?php

define('SELF', 'index.php');
define('EXT', '.php');
define('BASEPATH', str_replace("\\", "/", realpath(__DIR__ . '/../system/')) . '/');
define('FCPATH', realpath(__DIR__ . '/../') . '/');
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

if (is_dir($application_folder = realpath(__DIR__ . '/../application'))) {
    define('APPPATH', $application_folder . '/');
} else {
    if (!is_dir($application_folder = BASEPATH . $application_folder . '/')) {
        exit("Your application folder path does not appear to be set correctly.");
    }
    define('APPPATH', $application_folder);
}

require_once APPPATH . 'config/constants.php';
require_once FCPATH . 'vendor/autoload.php';
