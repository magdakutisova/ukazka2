<?php
namespace Book\Model;

use Zend\Db\TableGateway\TableGateway;

class FavoriteTable{
	
	protected $tableGateway;
	
	/*****
	 * Při konstrukci objektu nastaví instanci brány databázové tabulky.
	*/
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	public function favorite($idBook, $idUser){
		try{
			$this->tableGateway->insert(array(
					'idBook' => $idBook,
					'idUser' => $idUser,
					));
			return true;
		}
		catch(\Exception $e){
			return false;
		}
	}
	
}