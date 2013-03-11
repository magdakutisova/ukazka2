<?php
namespace Book\Model;

use Zend\Db\TableGateway\TableGateway;

class BookTable{
	
	protected $tableGateway;
	
	/*****
	 * Při konstrukci objektu nastaví instanci brány databázové tabulky.
	 */
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	/****
	 * Vrátí všechny knihy z databáze.
	 */
	public function fetchAll(){
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	
	/*****
	 * Nalezne knihu podle zadaného ID.
	 */
	public function find($id){
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('idBook' => $id));
		$row = $rowset->current();
		if(!$row){
			throw new \Exception("Kniha $id nebyla nalezena.");
		}
		return $row;
	}
	
	/*****
	 * Uloží knihu do databáze.
	 */
	public function save(Book $book){
		$data = $book->getArrayCopy();
		$id = (int)$book->idBook;
		if(0 == $id){
			unset($data['idBook']);
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
	
	/*****
	 * Smaže knihu z databáze.
	 */
	public function delete($id){
		$id = (int) $id;
		$this->tableGateway->delete(array('idBook' => $id));
	}
	
}