<?php
namespace User\Model;

/**
 * Třída mapující záznam tabulky user na objekt User.
 * @author Magda Kutišová
 *
 */
class User{
	public $idUser;
	public $email;
	public $password;
	public $salt;
	public $role;
	
	/**
	 * Načte proměnné třídy z pole.
	 * @param unknown $data pole proměnných
	 */
	public function exchangeArray($data){
		$this->idUser = (isset($data['idUser'])) ? $data['idUser'] : null;
		$this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->password = (isset($data['password'])) ? $data['password'] : null;
		$this->salt = (isset($data['salt'])) ? $data['salt'] : null;
		$this->role = (isset($data['role'])) ? $data['role'] : null;
	}
}