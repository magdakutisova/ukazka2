<?php
namespace User\Model;

class User{
	public $idUser;
	public $email;
	public $password;
	public $salt;
	public $role;
	public $firstName;
	public $surname;
	public $street;
	public $number;
	public $country;
	
	/*****
	 * Načte proměnné třídy z pole.
	 */
	public function exchangeArray($data){
		$this->idUser = (isset($data['idUser'])) ? $data['idUser'] : null;
		$this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->password = (isset($data['password'])) ? $data['password'] : null;
		$this->salt = (isset($data['salt'])) ? $data['salt'] : null;
		$this->role = (isset($data['role'])) ? $data['role'] : null;
		$this->firstName = (isset($data['firstName'])) ? $data['firstName'] : null;
		$this->surname = (isset($data['surname'])) ? $data['surname'] : null;
		$this->street = (isset($data['street'])) ? $data['street'] : null;
		$this->number = (isset($data['number'])) ? $data['number'] : null;
		$this->country = (isset($data['country'])) ? $data['country'] : null;
	}
}