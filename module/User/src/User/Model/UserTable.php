<?php
namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Třída pro manipulaci s databázovou tabulkou user.
 * @author Magda Kutišová
 *
 */
class UserTable{
	
	protected $tableGateway;
	
	/**
	 * Při konstrukci objektu nastaví bránu databázové tabulky.
	 * @param TableGateway $tableGateway brána databázové tabulky
	 */
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Nalezne uživatele podle zadaného ID.
	 * @param unknown $id ID uživatele
	 * @throws \Exception pokud uživatel není nalezen
	 * @return mixed záznam z databáze
	 */
	public function find($id){
		$id = (int) $id;
		$rowset = $this->tableGateway->select(array('idUser' => $id));
		$row = $rowset->current();
		if(!$row){
			throw new \Exception("Uživatel $id nebyl nalezen.");
		}
		return $row;
	}
	
	/**
	 * Nalezne uživatele podle emailové adresy.
	 * @param unknown $email emailová adresa uživatele
	 * @throws \Exception pokud uživatel není nalezen
	 * @return mixed záznam z databáze
	 */
	public function findByEmail($email){
		$rowset = $this->tableGateway->select(array('email' => $email));
		$row = $rowset->current();
		if(!$row){
			throw new \Exception("Uživatel $email nebyl nalezen.");
		}
		return $row;
	}
	
	/**
	 * Uloží uživatele do databáze.
	 * @param User $user uživatel
	 * @throws \Exception pokud ID pro update uživatele není platné
	 */
	public function save(User $user){
		$data = array(
				'email' => $user->email,
				'password' => $user->password,
				'salt' => $user->salt,
				'role' => $user->role,
				);
		$id = (int)$user->idUser;
		if(0 == $id){
			$this->tableGateway->insert($data);
		}
		else{
			if($this->find($id)){
				$this->tableGateway->update($data, array('idUser' => $id));
			}
			else{
				throw new \Exception("Zadané id neexistuje.");
			}
		}
	}
	
}