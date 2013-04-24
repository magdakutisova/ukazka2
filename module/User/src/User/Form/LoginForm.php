<?php
namespace User\Form;

use Zend\Form\Form;

/**
 * Třída obsahující nastavení formuláře pro přihlášení.
 * @author Magda Kutišová
 *
 */
class LoginForm extends Form{
	
	/**
	 * Vytvoří formulář.
	 * @param string $name jméno formuláře
	 */
	public function __construct($name = null){
		parent::__construct('login');
		$this->setAttribute('method', 'post');
		$this->add(array(
				'name' => 'email',
				'attributes' => array(
						'type' => 'text',
						),
				'options' => array(
						'label' => 'E-mail',
						),
				));
		$this->add(array(
				'name' => 'password',
				'attributes' => array(
						'type' => 'password',
						),
				'options' => array(
						'label' => 'Heslo',
						),
				));
		$this->add(array(
				'name' => 'login',
				'attributes' => array(
						'type' => 'submit'
				),
				'options' => array(
						'label' => 'Přihlásit',
				),
		));
		$this->add(array(
				'name' => 'csrf',
				'attributes' => array(
						'type' => 'csrf',
				),
		));
	}
	
}