<?php
namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable{
	
	protected $tableGateway;
	
	/*****
	 * Při konstrukci objektu nastaví instanci brány databázové tabulky.
	*/
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	/****
	 * Nalezne uživatele podle zadaného ID.
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
	
	/*****
	 * Nalezne uživatele podle emailové adresy.
	 */
	public function findByEmail($email){
		$rowset = $this->tableGateway->select(array('email' => $email));
		$row = $rowset->current();
		if(!$row){
			throw new \Exception("Uživatel $email nebyl nalezen.");
		}
		return $row;
	}
	
	/*****
	 * Uloží uživatele do databáze.
	 */
	public function save(User $user){
		$data = array(
				'email' => $user->email,
				'password' => $user->password,
				'salt' => $user->salt,
				'role' => $user->role,
				'firstName' => $user->firstName,
				'surname' => $user->surname,
				'street' => $user->street,
				'number' => $user->number,
				'country' => $user->country,
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