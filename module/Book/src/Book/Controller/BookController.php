<?php
namespace Book\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BookController extends AbstractActionController{
	
	protected $bookTable;
	
	public function getBookTable(){
		if(!$this->bookTable){
			$sm = $this->getServiceLocator();
			$this->bookTable = $sm->get('Book\Model\BookTable');
		}
		return $this->bookTable;
	}
	
	public function indexAction(){
		return new ViewModel(array(
				'books' => $this->getBookTable()->fetchAll(),
				));
	}
	
	public function detailAction(){
		
	}
	
	public function newAction(){
		
	}
	
	public function editAction(){
		
	}
	
	public function deleteAction(){
		
	}
	
}