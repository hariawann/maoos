<?php
namespace Application\Config;
use Framework\Engine\Services\DI\ServiceLocator as sl;

sl::bind("bootex","Framework\Boots\Booting");
sl::bind("bmark","Framework\Tools\Benchmark\Benchmark");
