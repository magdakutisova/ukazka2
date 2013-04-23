<?php
namespace Book\Form;

use Zend\Form\Form;
/**
 * Třída obsahující nastavení formuláře pro zadávání údajů o knihách.
 * @author Magda Kutišová
 *
 */
class BookForm extends Form{
	
	/**
	 * Vytvoří formulář.
	 * @param string $name jméno formuláře
	 */
	public function __construct($name = null){
		parent::__construct('book');
		$this->setAttribute('method', 'post');
		$this->setAttribute('enctype', 'multipart/form-data');
		
		$this->add(array(
				'name' => 'idBook',
				'attributes' => array(
						'type' => 'hidden',
						),
				));
		$this->add(array(
				'name' => 'name',
				'attributes' => array(
						'type' => 'text',
						),
				'options' => array(
						'label' => 'Název knihy:',
						),
				));
		$this->add(array(
				'name' => 'author',
				'attributes' => array(
						'type' => 'text',
						),
				'options' => array(
						'label' => 'Autor:',
						),
				));
		$this->add(array(
				'name' => 'description',
				'attributes' => array(
						'type' => 'textArea',
						),
				'options' => array(
						'label' => 'Popis:',
						),
				));
		$this->add(array(
				'name' => 'price',
				'attributes' => array(
						'type' => 'text',
						),
				'options' => array(
						'label' => 'Cena v Kč:',
						),
				));
		$this->add(array(
				'name' => 'stock',
				'attributes' => array(
						'type' => 'text',
						),
				'options' => array(
						'label' => 'Počet ks na skladě:'
						),
				));
		$this->add(array(
				'name' => 'image',
				'attributes' => array(
						'type' => 'file',
						),
				'options' => array(
						'label' => 'Obálka knihy:'
						),
				));
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type' => 'submit',
						'value' => 'Uložit knihu',
						'id' => 'submitbutton'
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