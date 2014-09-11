<?php 
include 'database.php'; 
class Books extends Illuminate\Database\Eloquent\Model {
	public $timestamps = true;
}
// Create the Books model 


// Grab a book with an id of 1 
// $book = Books::find(1); 
// var_dump($book);

// Change some stuff 
// $book->name = "The Best Book in the World";
// $book->author = "Ed Zynda";

// Save it to the database
// $book->save();
// $books = Books::all();
$books = Books::find(1);
$books->touch();
$books = Books::find(2);
$books->touch();
$books = Books::find(3);
$books->touch();
// foreach ($books as $book)
// {
// 	echo $book->author;
// }

$books = Books::all();
$bar = $books->toJson();
var_dump($bar);

var_dump($capsule);