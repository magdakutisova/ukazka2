<?php
namespace Book\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Book\Model\Book;
use Book\Form\BookForm;

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
		$form = new BookForm();
		$form->get('submit')->setValue('Přidat');
		
		$request = $this->getRequest();
		if ($request->isPost()){
			$book = new Book();
			$form->setInputFilter($book->getInputFilter());
			$form->setData($request->getPost());
			
			if($form->isValid()){
				$book->exchangeArray($form->getData());
				$this->getBookTable()->save($book);
				
				return $this->redirect()->toRoute('book');
			}
		}
		return array('form' => $form);
	}
	
	public function editAction(){
		
	}
	
	public function deleteAction(){
		
	}
	
}