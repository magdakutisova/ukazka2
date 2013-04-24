<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\RegisterForm;
use User\Model\User;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use User\Model\RegisterFilter;
use User\Form\LoginForm;
use User\Model\LoginFilter;
use Book\Model\BookTable;

/**
 * Controller pro manipulaci s uživateli.
 * @author Magda Kutišová
 *
 */
class UserController extends AbstractActionController{
	
	protected $userTable;
	protected $bookTable;
	
	/**
	 * Získá ze ServiceManageru instanci třídy pro manipulaci s databázovou tabulkou user.
	 * @return Ambigous <object, multitype:> instance třídy pro manipulaci s databázovou tabulkou user
	 */
	public function getUserTable(){
		if(!$this->userTable){
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('User\Model\UserTable');
		}
		return $this->userTable;
	}
	
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
	 * Akce pro registraci nových uživatelů.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:\User\Form\RegisterForm multitype: přesměrování nebo proměnné pro view
	 */
	public function registerAction(){
		$form = new RegisterForm();
		$form->get('create')->setValue('Zaregistrovat');
		$request = $this->getRequest();
		if($request->isPost()){
			$user = new User();
			$registerFilter = new RegisterFilter();
			$form->setInputFilter($registerFilter->getInputFilter());
			$form->setData($request->getPost());
			if($form->isValid()){
				$user->exchangeArray($form->getData());
				try{
					$existingUser = $this->getUserTable()->findByEmail($form->get('email')->getValue());
					$this->flashMessenger()->addMessage('Uživatel s tímto emailem již existuje');
					return $this->redirect()->toRoute('user', array(
							'action' => 'register',
					));
				}
				catch(\Exception $e){
					$salt = $this->generateSalt();
					$password = $this->encrypt($user->password, $salt);
					$salt = base64_encode($salt);
					$user->password = $password;
					$user->salt = $salt;
					$user->role = 2;
					$this->getUserTable()->save($user);
					$this->process(array(
							'email' => $user->email,
							'password' => $form->get('password')->getValue(),
							));
					return $this->redirect()->toRoute('book');
				}
			}
		}
		
		return array(
				'form' => $form,
				'flashMessages' => $this->flashMessenger()->getMessages(),
		);
	}
	
	/**
	 * Akce pro přihlášení uživatele.
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:\User\Form\LoginForm multitype: přesměrování nebo proměnné pro view
	 */
	public function loginAction(){
		$form = new LoginForm();
		$form->get('login')->setValue('Přihlásit');
		$request = $this->getRequest();
		if($request->isPost()){
			$loginFilter = new LoginFilter();
			$form->setInputFilter($loginFilter->getInputFilter());
			$form->setData($request->getPost());
			if($form->isValid()){
				if($this->process($form->getData())){
					$this->flashMessenger()->addMessage('Přihlášení bylo úspěšné.');
					return $this->redirect()->toRoute('book');
				}
				else{
					$this->flashMessenger()->addMessage('Chybné uživatelské jméno nebo heslo.');
					return $this->redirect()->toRoute('user', array('action' => 'login'));
				}
			}
		}	
		return array(
				'form' => $form,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
	/**
	 * Akce pro odhlášení uživatele.
	 * @return multitype:multitype: proměnné pro view
	 */
	public function logoutAction(){
		$auth = new AuthenticationService();
		$auth->clearIdentity();
		$this->redirect()->toRoute('book');
		return array(
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
	/**
	 * Akce pro zobrazení profilu uživatele.
	 * @return multitype:NULL
	 */
	public function profileAction(){
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()){
			$this->redirect()->toRoute('acl');
		}
		$bookTable = $this->getBookTable();
		return array(
				'email' => $auth->getIdentity()->email,
				'favorites' => $bookTable->fetchFavorites($auth->getIdentity()->idUser),
				);
	}
	
	/**
	 * Funkce generující sůl pro zašifrování hesla.
	 * @return string sůl
	 */
	private function generateSalt()
	{
		$salt = mcrypt_create_iv ( 64 );
		return $salt;
	}
	
	/**
	 * Funkce, která zašifruje heslo pomocí soli.
	 * @param unknown $password heslo k zašifrování
	 * @param unknown $salt sůl pro zašifrování
	 * @return string zašifrované heslo
	 */
	private function encrypt($password, $salt)
	{
		$password = hash ( 'sha256', $salt . $password );
		return $password;
	}
	
    /**
     * Funkce, která zpracuje hodnoty zadané uživatelem a pokud je to možné, uživatele přihlásí.
     * 
     * @param unknown $values uživatelské jméno a heslo
     * @return boolean true, pokud byl uživatel přihlášen, false, pokud nastala chyba
     */
	private function process($values){
		try{
			$user = $this->getUserTable()->findByEmail($values['email']);
			$password = $values['password'];
			$salt = base64_decode($user->salt);
			$password = $this->encrypt($password, $salt);
				
			$adapter = $this->getAuthAdapter();
			$adapter->setIdentity($values['email'])
			->setCredential($password);
				
			$auth = new AuthenticationService();
			$result = $auth->authenticate($adapter);
				
			if($result->isValid()){
				$loggedUser = $adapter->getResultRowObject();
				$auth->getStorage()->write($loggedUser);
				return true;
			}
			return false;
		}
		catch(\Exception $e){
			$this->flashMessenger()->addMessage('Uživatel s emailem "' . $values['email'] . '" neexistuje.');
			$this->redirect()->toRoute('user', array(
					'action' => 'login',
			));
		}	
		
	}
	
	/**
	 * Vrátí nakonfigurovaný adaptér k tabulce user určený k přihlašování.
	 * @return \Zend\Authentication\Adapter\DbTable  adaptér
	 */
	private function getAuthAdapter(){
		$dbAdapter = new DbAdapter(array(
				'driver' => 'Pdo_Mysql',
				'database' => 'ukazka',
				'username' => 'root',
				'password' => '',
				));
		$authAdapter = new AuthAdapter($dbAdapter);
		
		$authAdapter->setTableName('user')
			->setIdentityColumn('email')
			->setCredentialColumn('password');
		
		return $authAdapter;
	}
}