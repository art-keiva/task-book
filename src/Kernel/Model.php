<?php

namespace App\Kernel;

abstract class Model
{
	public $conn;
	
	function __construct()
	{
		$connectionParams = array(
		    // 'url' => 'mysql://user:secret@localhost/mydb',
		    'url' => 'sqlite:///' . $_ENV['APP_DIR'] . '/data/database.sqlite',
		);
		$this->conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
	}
}