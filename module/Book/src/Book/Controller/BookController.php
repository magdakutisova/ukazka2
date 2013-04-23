<?php
namespace Book\Controller;

use Book\Model\BookTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\InputFilter;
use Book\Model\Book;
use Book\Form\BookForm;
use Acl\Controller\Plugin\AclPlugin;
use Zend\Authentication\AuthenticationService;
use Acl\Library\AclDefinition as MyAcl;

/**
 * Controller pro manipulaci s knihami.
 * @author Magda Kutišová
 *
 */
class BookController extends AbstractActionController{
	
	protected $bookTable;
	protected $favoriteTable;
	
	/**
	 * Získá ze ServiceManageru instanci třídy pro manipulaci s databázovou tabulkou book.
	 * @return Ambigous <object, multitype:> instance třídy pro manipulaci s databázovou tabulkou book
	 */
	public function getBookTable(){
		if(!$this->bookTable){
			$sm = $this->getServiceLocator();
			$this->bookTable = $sm->get('Book\Model\BookTable');
		}
		return $this->bookTable;
	}
	
	/**
	 * Získá ze ServiceManageru instanci třídy pro manipulaci s databázovou tabulkou favorite.
	 * @return Ambigous <object, multitype:> instance třídy pro manipulaci s databázovou tabukou favorite
	 */
	public function getFavoriteTable(){
		if(!$this->favoriteTable){
			$sm = $this->getServiceLocator();
			$this->favoriteTable = $sm->get('Book\Model\FavoriteTable');
		}
		return $this->favoriteTable;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction(){
		$auth = new AuthenticationService();
		$role = '';
		if($auth->hasIdentity()){
			$role = $auth->getIdentity()->role;
		}
		else{
			$role = 3;
		}
		$books = $this->getBookTable()->fetchAll();
		return array(
				'books' => $books,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				'acl' => new MyAcl(),
				'role' => $role,
				);
	}
	
	/**
	 * Akce pro zobrazení detailu knihy.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:multitype: unknown přesměrování nebo proměnné pro view
	 */
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
	
	/**
	 * Akce pro přidání nové knihy do databáze.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:\Book\Form\BookForm multitype: přesměrování nebo proměnné pro view
	 */
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
	
	/**
	 * Akce pro editaci knihy.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:number \Book\Form\BookForm multitype: přesměrování nebo proměnné pro view
	 */
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
	
	/**
	 * Akce pro smazání knihy.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:number multitype: NULL přesměrování nebo proměnné pro view
	 */
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
	
	/**
	 * Akce pro přidání knihy do seznamu oblíbených.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:multitype: přesměrování nebo proměnné pro view
	 */
	public function favoriteAction(){
		$idBook = (int) $this->params()->fromRoute('id', 0);
		if(!$idBook){
			return $this->redirect()->toRoute('book');
		}
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()){
			return $this->redirect()->toRoute('acl');
		}
		$idUser = $auth->getIdentity()->idUser;
		$favoriteTable = $this->getFavoriteTable();
		$result = $favoriteTable->favorite($idBook, $idUser);
		if($result){
			$this->flashMessenger()->addMessage('Kniha byla přidána do seznamu oblíbených');
		}
		else{
			$this->flashMessenger()->addMessage('Tuto knihu již máte v seznamu oblíbených');
		}
		$this->redirect()->toRoute('book');
		return array(
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
}