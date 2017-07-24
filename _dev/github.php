<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 7/25/17
 * Time: 12:38 AM
 */


require "../vendor/autoload.php";


$github = new \Joomla\Github\Github();

$ret = $github->repositories->getListTags("symfony", "yaml");
print_r ($ret);