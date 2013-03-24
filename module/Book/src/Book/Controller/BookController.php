<?php
namespace Book\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\InputFilter;
use Book\Model\Book;
use Book\Form\BookForm;
use Acl\Controller\Plugin\AclPlugin;
use Zend\Authentication\AuthenticationService;
use Acl\Library\AclDefinition as MyAcl;

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
		$auth = new AuthenticationService();
		$role = '';
		if($auth->hasIdentity()){
			$role = $auth->getIdentity()->role;
		}
		else{
			$role = 3;
		}
		return array(
				'books' => $this->getBookTable()->fetchAll(),
				'flashMessages' => $this->flashMessenger()->getMessages(),
				'acl' => new MyAcl(),
				'role' => $role,
				);
	}
	
	public function detailAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		if(!$id){
			return $this->redirect()->toRoute('book');
		}
		$book = $this->getBookTable()->find($id);
		return array(
				'book' => $book,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
	public function newAction(){
		$form = new BookForm();
		$form->get('submit')->setValue('Přidat');
		
		$request = $this->getRequest();
		if ($request->isPost()){
			$post = array_merge_recursive(
					$request->getPost()->toArray(),
					$request->getFiles()->toArray()
					);
			$book = new Book();
			
			$form->setInputFilter($book->getInputFilter());
			$form->setData($post);
			$newFileName = '';
						
			if($post['image']['name'] != ''){
				$originalFileName = pathinfo($post['image']['name']);
				$author = $post['author'];
				$name = $post['name'];
				$new = preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $author . ' ' . $name));
				$newFileName = $new . '.' . $originalFileName['extension'];
				
				$inputFilter = $form->getInputFilter();
				
				$fileInput = new InputFilter\FileInput('image');
				$fileInput->setRequired(false);
				$fileInput->getFilterChain()->attachByName(
						'filerenameupload',
						array(
								'target' => PUBLIC_PATH . '/images/' . $newFileName,
								'overwrite' => true,
								)
						);
				$inputFilter->add($fileInput);
			}
			
			if($form->isValid()){
				$book->exchangeArray($form->getData());
				$book->image = $newFileName;
				$this->getBookTable()->save($book);
				$this->flashMessenger()->addMessage('Kniha přidána');	
				return $this->redirect()->toRoute('book');
			}
		}
		return array(
				'form' => $form,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
	public function editAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		if(!$id){
			return $this->redirect()->toRoute('book', array(
					'action' => 'new',
					));
		}
		$book = $this->getBookTable()->find($id);
		$image = $book->image;
		
		$form = new BookForm();
		$form->bind($book);
		$form->get('submit')->setAttribute('value', 'Upravit');
		
		$request = $this->getRequest();
		if($request->isPost()){
			$post = array_merge_recursive(
					$request->getPost()->toArray(),
					$request->getFiles()->toArray()
			);
			$form->setInputFilter($book->getInputFilter());
			$form->setData($post);
			$newFileName = '';
				
			if($post['image']['name'] != ''){
				$originalFileName = pathinfo($post['image']['name']);
				$author = $post['author'];
				$name = $post['name'];
				$new = preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $author . ' ' . $name));
				$newFileName = $new . '.' . $originalFileName['extension'];
			
				$inputFilter = $form->getInputFilter();
			
				$fileInput = new InputFilter\FileInput('image');
				$fileInput->setRequired(false);
				$fileInput->getFilterChain()->attachByName(
						'filerenameupload',
						array(
								'target' => PUBLIC_PATH . '/images/' . $newFileName,
								'overwrite' => true,
						)
				);
				$inputFilter->add($fileInput);
			}
			
			if($form->isValid()){
				$data = $form->getData();
				if($newFileName == ''){
					$data->image = $image;
				}
				else{
					$data->image = $newFileName;
				}
				$this->getBookTable()->save($data);
				$this->flashMessenger()->addMessage('Kniha upravena');
				
				return $this->redirect()->toRoute('book');
			}
		}
		
		return array(
				'id' => $id,
				'form' => $form,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
	public function deleteAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		if(!$id){
			return $this->redirect()->toRoute('book');
		}		
		$request = $this->getRequest();
		if($request->isPost()){
			$del = $request->getPost('del', 'Ne');
			if($del == 'Ano'){
				$id = (int) $request->getPost('id');
				$this->getBookTable()->delete($id);
			}
			$this->flashMessenger()->addMessage('Kniha smazána');	
			return $this->redirect()->toRoute('book');
		}
		return array(
				'id' => $id,
				'book' => $this->getBookTable()->find($id),
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
}