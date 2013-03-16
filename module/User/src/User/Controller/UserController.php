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

class UserController extends AbstractActionController{
	
	protected $userTable;
	
	public function getUserTable(){
		if(!$this->userTable){
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('User\Model\UserTable');
		}
		return $this->userTable;
	}
	
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
	
	public function logoutAction(){
		$auth = new AuthenticationService();
		$auth->clearIdentity();
		$this->redirect()->toRoute('book');
		return array(
				'flashMessages' => $this->flashMessenger()->getMessages(),
				);
	}
	
	private function generateSalt()
	{
		$salt = mcrypt_create_iv ( 64 );
		return $salt;
	}
	
	private function encrypt($password, $salt)
	{
		$password = hash ( 'sha256', $salt . $password );
		return $password;
	}
	
	private function process($values){
		$user = $this->getUserTable()->findByEmail($values['email']);
		if(!$user){
			$this->flashMessenger()->addMessage('Uživatel s emailem "' . $values['email'] . '" neexistuje.');
			$this->redirect()->toRoute('user', array(
					'action' => 'login',
					));
		}
		else{
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
	}
	
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