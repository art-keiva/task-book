<?php

namespace App\Model;

use App\Kernel\Model;
use App\Pagination\Paginator;

class Task extends Model
{
	public function addTask($data)
	{
		$queryBuilder = $this->conn->createQueryBuilder();

		$queryBuilder
			->insert('tasks')
			->setValue('fullname', '?')
			->setValue('email', '?')
			->setValue('description', '?')
			->setParameter(0, $data['fullname'])
			->setParameter(1, $data['email'])
			->setParameter(2, $data['description'])
		;

		$queryBuilder->execute();

		return $this->conn->lastInsertId();
	}

	public function getTaskById(int $id)
	{
		$sql = "SELECT * FROM tasks WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue("id", $id);
		$stmt->execute();

		return $stmt->fetch();
	}

	public function getLatestTasks(array $criteria = [], array $orderBy = [], int $page = 1)
	{
		$queryBuilder = $this->conn->createQueryBuilder();

		$queryBuilder
		    ->select('*')
		    ->from('tasks')
		;

		if (!empty($criteria)) {
			foreach ($criteria as $fieldAndvalue) {
				$queryBuilder->where($fieldAndvalue);
			}
		}

		if (!empty($orderBy)) {
			foreach ($orderBy as $parameter) {
				$param = explode('=', $parameter);
				$queryBuilder->orderBy($param[0], $param[1]);
			}
		}

		return (new Paginator($queryBuilder, 3))->paginate($page);
	}

	public function setStatusTask(int $id, bool $checked)
	{
		$queryBuilder = $this->conn->createQueryBuilder();

		$queryBuilder
			->update('tasks')
			->set('completed', '?')
			->where('id = ?')
			->setParameter(0, $checked)
			->setParameter(1, $id)
		;

		$queryBuilder->execute();
	}

	public function setTaskDescription(int $id, string $description)
	{
		$queryBuilder = $this->conn->createQueryBuilder();
			
		$queryBuilder
			->update('tasks')
			->set('description', '?')
			->set('edited', '?')
			->where('id = ?')
			->setParameter(0, $description)
			->setParameter(1, true)
			->setParameter(2, $id)
		;

		$oldDescription = $this->getTaskById($id);

		if ($oldDescription && $oldDescription['description'] !== $description) {
			$queryBuilder->execute();

			return true;
		}

		return false;
	}
}