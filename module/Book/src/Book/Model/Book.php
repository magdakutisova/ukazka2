<?php
namespace Book\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\FileInput;

/**
 * Třída mapující záznam tabulky book na objekt Book.
 * @author Magda Kutišová
 *
 */
class Book implements InputFilterAwareInterface{
	
	public $idBook;
	public $name;
	public $author;
	public $description;
	public $price;
	public $stock;
	public $image;
	protected $inputFilter;
	
	/**
	 * Načte proměnné třídy z pole.
	 * @param unknown $data pole proměnných
	 */
	public function exchangeArray($data){
		$this->idBook = (isset($data['idBook'])) ? $data['idBook'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		$this->author = (isset($data['author'])) ? $data['author'] : null;
		$this->description = (isset($data['description'])) ? $data['description'] : null;
		$this->price = (isset($data['price'])) ? $data['price'] : null;
		$this->stock = (isset($data['stock'])) ? $data['stock'] : null;
		$this->image = (isset($data['image'])) ? $data['image'] : null;
	}
	
	/**
	 * Vrátí proměnné třídy jako pole
	 * @return multitype: pole proměnných
	 */
	public function getArrayCopy(){
		return get_object_vars($this);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
	 */
	public function setInputFilter(InputFilterInterface $inputFilter){
		throw new \Exception("Neimplementováno");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\InputFilter\InputFilterAwareInterface::getInputFilter()
	 */
	public function getInputFilter(){
		if(!$this->inputFilter){
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'idBook',
					'required' => true,
					'filters' => array(
							array('name' => 'Int'),
							),
					)));
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'name',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							),
					'validators' => array(
							array(
									'name' => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 200,
											),
									),
							),
					)));
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'author',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							),
					'validators' => array(
							array(
									'name' => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100,
											),
									),
							),
					)));
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'price',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							),
					'validators' => array(
							array(
									'name' => 'Float',
									'options' => array(
											'locale' => 'cs',
											),
									),
							),
					)));
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'stock',
					'required' => true,
					'filters' => array(
							array('name' => 'StringTrim'),
							),
					'validators' => array(
							array(
									'name' => 'Int',
									'options' => array(
											'locale' => 'cs',
											),
									),
							array(
									'name' => 'GreaterThan',
									'options' => array(
											'min' => -1,
											),
									),
							),
					)));
			
			$inputFilter->add($factory->createInput(array(
					'name' => 'image',
					'type' => 'Zend\InputFilter\FileInput',
					'required' => false,
					'validators' => array(
							array(
									'name' => 'Zend\Validator\File\IsImage',
									),
							array(
									'name' => 'Zend\Validator\File\Count',
									'options' => array(
											'max' => 1,
											),
									),
							),							
					)));

			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
	
}