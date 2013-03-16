<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class RegisterForm extends Form{
	protected $inputFilter;
	
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
	
	public function getInputFilter(){
		if(!$this->inputFilter){
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'email',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							array('name' => 'StringtoLower'),
							),
					'validators' => array(
							array('name' => 'EmailAddress'),
							array(
									'name' => 'StringLength',
									'options' => array(
											'min' => 1,
											'max' => 255,
											),
									),
							),
					)));
			$inputFilter->add($factory->createInput(array(
					'name' => 'password',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							),
					'validators' => array(
							array('name' => 'Alnum'),
							array(
									'name' => 'StringLength',
									'options' => array(
											'min' => 1,
											'max' => 50,
											),
									),
							),
					)));
			$inputFilter->add($factory->createInput(array(
					'name' => 'confirmPassword',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							),
					'validators' => array(
							array(
									'name' => 'Identical',
									'options' => array(
											'token' => 'password',
											),
									),
							),
					)));
		}
	}
}