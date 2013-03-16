<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\RegisterForm;

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
		return array('form' => $form);
	}
	
	public function loginAction(){
		
	}
	
	public function logoutAction(){
		
	}
}