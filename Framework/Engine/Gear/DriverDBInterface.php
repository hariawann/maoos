<?php
namespace Framework\Engine\Gear; 

Interface DriverDBInterface{
	function connect();
	function disconnect();
	function query($sql);
	function results($query, $type);
	function numRows($sql);
	function isAffected($sql);
}
