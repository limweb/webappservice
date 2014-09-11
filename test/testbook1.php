<?php 
include 'database.php'; 

class  product   extends  Illuminate\Database\Eloquent {

    public $timestamps = false;
    public $table = 'product';
}   



$p  = new  product();
$p->productName = 'name5';
$p->price = 200;
$p->qty = 5;
$p->primaryKey('productId');// = 'p5';
// print_r($p);
// exit();
var_dump($p);
$p->save();

 
// class Route extends  Illuminate\Database\Eloquent\Model {

//     // public static $table = 'routes';
//     public static $key = 'ROUTEID;

// }

// $r = new Route();
// $r->ROUTECODE = '0011';
// $r->ROUTENAME = 'test11';
// $r->save();




exit();
// Create the Books model 
class Books extends Illuminate\Database\Eloquent\Model {
    public $timestamps = true;
}
 
// Grab a book with an id of 1 
$book = Books::find(4); 
 
// Change some stuff 
$book->name = "The Best Book in the World";
$book->author = "Ed Zynda";
$book->touch();
// $book->created_at  = R::isoDate();
 
// Save it to the database
$book->save();

// $b = new Books();
// $b->name = 'test';
// $b->author="testauthor";
// $b->save();

// $rs = \Books::all();
// var_dump($rs);
// var_dump($book);