<?php
namespace Book\Model;

class Book{
	
	public $idBook;
	public $name;
	public $author;
	public $description;
	public $price;
	public $stock;
	public $image;
	
	public function exchangeArray($data){
		$this->idBook = (isset($data['idBook'])) ? $data['idBook'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->author = (isset($data['author'])) ? $data['author'] : null;
		$this->description = (isset($data['description'])) ? $data['description'] : null;
		$this->price = (isset($data['price'])) ? $data['price'] : null;
		$this->stock = (isset($data['stock'])) ? $data['stock'] : null;
		$this->image = (isset($data['image'])) ? $data['image'] : null;
	}
	
}