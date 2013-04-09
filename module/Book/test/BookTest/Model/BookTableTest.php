<?php
namespace BookTest\Model;

use Book\Model\BookTable;
use Book\Model\Book;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class BookTableTest extends PHPUnit_Framework_TestCase{
	
	public function testBookInsertedIntoDatabase(){
		$bookData = array(
				'name' => 'test',
				'author' => 'test',
				'price' => 100,
				'stock' => 10,
				'description' => 'test',
				'image' => 'test.jpg',
				);
		$book = new Book();
		$book->exchangeArray($bookData);
		
		$mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('insert'), array(), '', false);
		$mockTableGateway->expects($this->once())
						 ->method('insert')
						 ->with($bookData);
		
		$bookTable = new BookTable($mockTableGateway);
		$bookTable->save($book);
	}
	
	public function testBookDelete(){
		$mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('delete'), array(), '', false);
		$mockTableGateway->expects($this->once())
						 ->method('delete')
						 ->with(array('idBook' => 3));
		
		$bookTable = new BookTable($mockTableGateway);
		$bookTable->delete(3);
	}
	
}