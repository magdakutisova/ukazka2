<?php
namespace Book\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Třída pro práci s databázovou tabulkou favorite.
 * @author Magda Kutišová
 *
 */
class FavoriteTable{
	
	protected $tableGateway;
	
	/**
	 * Při konstrukci objektu nastaví bránu databázové tabulky.
	 * @param TableGateway $tableGateway brána databázové tabulky
	 */
	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Přidá knihu do oblíbených.
	 * @param unknown $idBook ID knihy
	 * @param unknown $idUser ID uživatele
	 * @return boolean true, pokud byla kniha přidána, false, pokud už ji uživatel v oblíbených měl
	 */
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