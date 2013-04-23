<?php
namespace Acl\Library;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;

/**
 * Třída s definicí zdrojů, rolí a práv přístupu.
 * @author Magda Kutišová
 *
 */
class AclDefinition extends Acl{
	
	/**
	 * Vytvoří instanci Acl a nastaví zdroje, role a práva.
	 */
	public function __construct(){
		$this->addResource('book');
		$this->addResource('user');
		$this->addResource('application');
		$this->addResource('acl');
		
		$guest = 3;
		$user = 2;
		$admin = 1;
		
		$this->addRole(new Role($guest));
		$this->addRole(new Role($user), $guest);
		$this->addRole(new Role($admin), $user);
		
		$this->allow($guest, 'user', array('user:register', 'user:login'));
		$this->allow($guest, 'book', array('book:index', 'book:detail'));
		$this->allow($guest, 'application');
		$this->allow($guest, 'acl');
		
		$this->allow($user, 'user', array('user:logout', 'user:profile'));
		$this->deny($user, 'user', array('user:login', 'user:register'));
		$this->allow($user, 'book', 'book:favorite');
		
		$this->allow($admin, 'book', array('book:new', 'book:edit', 'book:delete'));
	}
	
}