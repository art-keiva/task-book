<?php

namespace App\Controller;

use App\Kernel\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints as Assert;

class TaskController extends Controller
{
	public function index()
	{
		$orderBy = 'id';
		$sort = 'desc';
		$page = 1;

		/**
		 *	сохраняем сортировку
		 **/
		foreach ($this->request->query as $key => $value) {
			$key = strtolower($key);
			$value = strtolower($value);

			if ($key === 'orderby') {
				if ($value === 'fullname' || $value === 'email' || $value === 'completed' || $value === 'edited') {
					$orderBy = $value;
				}
			}
			if ($key === 'sort') {
				if ($value === 'asc' || $value === 'desc') {
					$sort = $value;
				}
			}
			if ($key === 'page') {
				$page = abs((int) $value);
			}
		}

		$paginator = $this->model->getLatestTasks([], [$orderBy . '=' . $sort], $page);

		/**
		 *	создаем форму для новой задачи
		 **/
		$form = $this->formFactory->createBuilder()
			->add('fullname', TextType::class, [
				'label' => 'Имя',
				'required' => false,
				'constraints' => new NotBlank([
					'message' => 'Поле не может быть пустым.',
				]),
			])
			->add('email', TextType::class, [
				'label' => 'Email',
				'required' => false,
				'constraints' => [
					new NotBlank([
						'message' => 'Поле не может быть пустым.',
					]),
					new Assert\Email([
						'message' => '"{{ value }}" - email не валиден.',
					]),
				],
			])
			->add('description', TextareaType::class, [
				'label' => 'Текст задачи',
				'required' => false,
				'constraints' => new NotBlank([
					'message' => 'Поле не может быть пустым.',
				]),
			])
			->add('submit', SubmitType::class, [
				'label' => 'Сохранить задачу',
			])
			->getForm();

		$form->handleRequest();

		$flashes = [];

		/**
		 *	обрабатываем введённые данные с формы,
		 *	выводим сообщение о результате
		 **/
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();

			$lastId = $this->model->addTask($data);

			if ($lastId) {
				$flashes['success'] = 'Задача успешно добавлена!';

				$this->session->set('flashes', $flashes);

				$redirect = $this->request->getPathInfo();

				header("Location: $redirect");
				exit;
			}
		}

		if ($this->session->has('flashes')) {
			$flashes = $this->session->get('flashes');
			$this->session->remove('flashes');
		}

		return $this->render('task/index.html.twig', [
			'paginator' => $paginator,
			'sorts' => $this->getSorts($orderBy, $sort),
			'form' => $form->createView(),
			'flashes' => $flashes,
		]);
	}

	/**
	 *	Меняем статус задачи с помощью аякса, только для админа
	 **/
	public function setStatus()
	{
		$id = $this->request->get('item') ?? null;
		$checked = $this->request->get('checked') ?? null;

		if (null !== $id && null !== $checked) {
			$response = new Response();

			$id = (int) $id;
			$checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);

			if ($this->session->has('auth')) {
				$this->model->setStatusTask($id, $checked);
				$msg = true;
			} else {
				$msg = false;
			}

			$response->setContent(json_encode([
				'status' => $msg,
			]));

			$response->headers->set('Content-Type', 'application/json');
			$response->send();
		}
	}

	/**
	 *	Меняем описание задачи с помощью аякса, только для админа
	 **/
	public function editDescription()
	{
		$id = $this->request->get('item') ?? null;
		$description = $this->request->get('description') ?? null;

		if (null !== $id && null !== $description) {
			$response = new Response();

			$id = (int) $id;
			$description = (string) $description;

			if ($this->session->has('auth')) {
				$editedStatus = $this->model->setTaskDescription($id, $description);
				$msg = true;
			} else {
				$msg = false;
			}

			$response->setContent(json_encode([
				'status' => $msg,
				'edited' => $editedStatus,
			]));

			$response->headers->set('Content-Type', 'application/json');
			$response->send();
		}
	}

	private function getSorts($orderBy, $sort)
	{
		$sorts = [];

		$sorts[] = [
			'title' => 'Имя пользователя',
			'orderBy' => 'fullname',
			'sort' => ($orderBy === 'fullname') ? ($sort === 'desc') ? 'asc' : 'desc' : 'asc',
			'width' => '15%',
		];

		$sorts[] = [
			'title' => 'Email',
			'orderBy' => 'email',
			'sort' => ($orderBy === 'email') ? ($sort === 'desc') ? 'asc' : 'desc' : 'asc',
			'width' => '15%',
		];

		$sorts[] = [
			'title' => 'Текст задачи',
			'orderBy' => '',
			'sort' => '',
			'width' => '35%',
		];

		$sorts[] = [
			'title' => 'Статус',
			'orderBy' => 'completed',
			'sort' => ($orderBy === 'completed') ? ($sort === 'desc') ? 'asc' : 'desc' : 'asc',
			'width' => '15%',
		];

		return $sorts;
	}
}