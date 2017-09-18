<?php
use Framework\Engine\Services\DI\ServiceLocator as sl;

function view($tamplate=null){

	$Viewer = sl::get('viewer');
	$Viewer->set($tamplate);
	
	return $Viewer;
}

function parser($template){
	$Parser = sl::get('parser');
	$Parser->parse($template);
}


