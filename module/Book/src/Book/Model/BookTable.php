<?php
namespace Book\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Cache\Storage\StorageInterface;
use Zend\Stdlib\Hydrator;
use Zend\Db\Sql\Sql;

/**
 * Třída pro manipulaci s databázovou tabulkou book.
 * @author Magda Kutišová
 *
 */
class BookTable{
	
	protected $tableGateway;
	protected $cache;
	
	/**
	 * Při konstrukci objektu nastaví bránu databázové tabulky.
	 * @param TableGateway $tableGateway brána databázové tabulky
	 */
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Nastaví cache
	 * @param StorageInterface $cache cache rozhraní
	 */
	public function setCache(StorageInterface $cache){
		$this->cache = $cache;
	}
	
	/**
	 * Vrátí všechny knihy z databáze
	 * @return multitype:object množina knih
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
	
	/**
	 * Vrátí oblíbené knihy uživatele.
	 * @param unknown $idUser ID uživatele
	 * @return multitype:object oblíbené knihy uživatele
	 */
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
	
	/**
	 * Nalezne knihu podle zadaného ID.
	 * @param unknown $id ID knihy
	 * @throws \Exception pokud kniha není nalezena
	 * @return mixed záznam z databáze
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
	
	/**
	 * Uloží knihu do databáze.
	 * @param Book $book kniha
	 * @throws \Exception pokud ID pro update knihy není platné
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
	
	/**
	 * Smaže knihu z databáze.
	 * @param unknown $id ID knihy
	 */
	public function delete($id){
		$id = (int) $id;
		$this->tableGateway->delete(array('idBook' => $id));
		$this->cache->removeItem('books');
		$this->cache->removeItem('book' . $id);
	}
	
}