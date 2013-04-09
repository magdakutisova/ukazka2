<?php
namespace BookTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Db\Adapter\Adapter;

class BookControllerTest extends AbstractHttpControllerTestCase{
	
	protected $traceError = true;
	
	public function setUp(){
		$this->setApplicationConfig(
				include dirname(dirname(dirname(dirname(dirname(__DIR__))))) .  '/config/application.config.php'
				);		
	}
	
	public function userLogin(){
		$dbAdapter = new Adapter(array(
				'driver' => 'Pdo_Mysql',
				'database' => 'ukazkaTest',
				'username' => 'root',
				'password' => '',
				));
		
		$serviceManager = $this->getApplicationServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('Zend\Db\Adapter\Adapter', $dbAdapter);
		
		$postData = array('email' => 'test@test.cz', 'password' => 'test');
		$this->dispatch('/user/login', 'POST', $postData);
		$this->assertRedirectTo('/book');
	}
	
	public function testRunIndexAction(){
		$bookTableMock = $this->getMockBuilder('Book\Model\BookTable')
			->disableOriginalConstructor()
			->getMock();
		
		$bookTableMock->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue(array()));
		
		$serviceManager = $this->getApplicationServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('Book\Model\BookTable', $bookTableMock); 
		
		$this->dispatch('/book');
		$this->assertResponseStatusCode(200);
		
		$this->assertModuleName('Book');
		$this->assertControllerName('Book\Controller\Book');
		$this->assertControllerClass('BookController');
		$this->assertMatchedRouteName('book');
	}
	
	public function testNewActionContainsForm(){
		$this->userLogin();
		$this->dispatch('/book/new');
		$this->assertModuleName('Book');
		$this->assertControllerName('Book\Controller\Book');
		$this->assertControllerClass('BookController');
		$this->assertActionName('new');
		$this->assertQueryCount('form#book', 1);
	}
	
	public function testAddedBookRedirectsToBookIndexAndContainsBook(){
		$this->userLogin();
		
		$bookTableMock = $this->getMockBuilder('Book\Model\BookTable')
							  ->disableOriginalConstructor()
							  ->getMock();
		$bookTableMock->expects($this->once())
					  ->method('save')
					  ->will($this->returnValue(null));
		
		$serviceManager = $this->getApplicationServiceLocator();
		$serviceManager->setAllowOverride(true);
		$serviceManager->setService('Book\Model\BookTable', $bookTableMock);
		
		$postData = array(
				'name' => 'test',
				'author' => 'test',
				'description' => 'test',
				'price' => 10,
				'stock' => 1,
				'image' => array('name' => ''),
				);
		$this->dispatch('/book/new', 'POST', $postData);
		//test selhává kvůli nemožnosti validovat formulář bez uploadu souboru, při zrušení
		//validace je funkční
		$this->assertResponseStatusCode(302);
		$this->assertRedirectTo('/book');
	}
	
}