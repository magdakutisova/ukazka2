<?php
namespace Book\Model;

use Zend\Db\TableGateway\TableGateway;

class BookTable{
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll(){
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	
	public function find($id){
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('idBook' => $id));
		$row = $rowset->current();
		if(!$row){
			throw new \Exception("Kniha $id nebyla nalezena.");
		}
		return $row;
	}
	
	public function save(Book $book){
		$data = array(
				'name' => $book->name,
				'author' => $book->author,
				'description' => $book->description,
				'price' => $book->price,
				'stock' => $book->stock,
				'image' => $book->image,
				);
		$id = (int)$book->idBook;
		if($id == 0){
			$this->tableGateway->insert($data);
		}
		else{
			if($this->find($id)){
				$this->tableGateway->update($data, array('idBook' => $id));
			}
			else{
				throw new \Exception("Zadané id neexistuje.");
			}
		}
	}
	
	public function delete($id){
		$this->tableGateway->delete(array('idBook' => $id));
	}
	
}