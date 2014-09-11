<?php
include "database.php";
class Contacts extends Illuminate\Database\Eloquent\Model {
	
	public $timestamps = true;
	
}


$books = Contacts::all();
// $bar = $books->toJson();
$bar = $books->toArray();
var_dump($bar);