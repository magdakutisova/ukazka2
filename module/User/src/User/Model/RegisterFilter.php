<?php
namespace User\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\Inputfilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RegisterFilter implements InputFilterAwareInterface{
	protected $inputFilter;
	
	public function setInputFilter(InputFilterInterface $inputFilter){
		throw new \Exception("Not used");
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
			return $inputFilter;
		}
	}
}