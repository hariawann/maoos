<?php
require __DIR__."/../../vendor/autoload.php";
use Framework\Engine\Services\DI\ServiceLocator as sl;

//initiation services
sl::init();

$app = sl::get("bootler");
