<?php
namespace User\Form;

use Zend\Form\Form;

class RegisterForm extends Form{
	
	public function __construct($name = null){
		parent::__construct('register');
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
				'name' => 'confirmPassword',
				'attributes' => array(
						'type' => 'password',
						),
				'options' => array(
						'label' => 'Heslo znovu',
						),
				));
		$this->add(array(
				'name' => 'create',
				'attributes' => array(
						'type' => 'submit'
						),
				'options' => array(
						'label' => 'Zaregistrovat',
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