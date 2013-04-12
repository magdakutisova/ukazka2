<?php
namespace Book\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Cache\Storage\StorageInterface;
use Zend\Stdlib\Hydrator;
use Zend\Db\Sql\Sql;

class BookTable{
	
	protected $tableGateway;
	protected $cache;
	
	/*****
	 * Při konstrukci objektu nastaví instanci brány databázové tabulky.
	 */
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	/******
	 * Nastaví cache.
	 */
	public function setCache(StorageInterface $cache){
		$this->cache = $cache;
	}
	
	/****
	 * Vrátí všechny knihy z databáze.
	 */
	public function fetchAll(){
		if(($resultSet = $this->cache->getItem('books')) == FALSE){
			$resultSet = $this->tableGateway->select();
			$resultSet = $resultSet->toArray();
			$this->cache->setItem('books', $resultSet);			
		}
		$books = array();
		$hydrator = new Hydrator\ArraySerializable();
		foreach($resultSet as $result){
			$books[] = $hydrator->hydrate($result, new Book());
		}
		return $books;
	}
	
	public function fetchFavorites($idUser){
		$sql = new Sql($this->tableGateway->getAdapter());
		$select = $sql->select()
			->from('book')
			->join('favorite', 'book.idBook = favorite.idBook')
			->where(array('idUser' => $idUser));
		$statement = $sql->prepareStatementForSqlObject($select);
		$resultSet = $statement->execute();
		$books = array();
		$hydrator = new Hydrator\ArraySerializable();
		foreach($resultSet as $result){
			$books[] = $hydrator->hydrate($result, new Book());
		}
		return $books;
	}
	
	/*****
	 * Nalezne knihu podle zadaného ID.
	 */
	public function find($id){
		$id = (int) $id;
		if(($result = $this->cache->getItem('book' . $id)) == FALSE){
			$rowset = $this->tableGateway->select(array('idBook' => $id));
			$result = $rowset->current();
			if(!$result){
				throw new \Exception("Kniha $id nebyla nalezena.");
			}
			$this->cache->setItem('book' . $id, $result);
		}
		return $result;
	}
	
	/*****
	 * Uloží knihu do databáze.
	 */
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
		if(0 == $id){
			$this->tableGateway->insert($data);
			$this->cache->removeItem('books');
		}
		else{
			if($this->find($id)){
				$this->tableGateway->update($data, array('idBook' => $id));
				$this->cache->removeItem('books');
				$this->cache->removeItem('book' . $id);
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
		$this->cache->removeItem('books');
		$this->cache->removeItem('book' . $id);
	}
	
}