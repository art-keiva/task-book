<?php

namespace App\Model;

use App\Kernel\Model;

class User extends Model
{
	public function getUser(array $data)
	{
		$queryBuilder = $this->conn->createQueryBuilder();

		$queryBuilder
			->select('*')
		    ->from('users')
			->where('username = :username')
			->setParameter('username', $data['username'])
		;

		return $queryBuilder->execute()->fetch();
	}
}