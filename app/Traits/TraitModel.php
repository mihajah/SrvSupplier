<?php
namespace App\Traits;

trait TraitModel
{
	public static function getProp($prop)
	{
		return (new self)->$prop;
	}
}
?>