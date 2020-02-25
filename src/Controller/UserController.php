<?php

namespace App\Controller;

use App\Kernel\Controller;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends Controller
{
	public function index()
	{
		$form = $this->formFactory->createBuilder()
			->add('username', TextType::class, [
				'label' => 'Имя пользователя',
				'required' => false,
				'constraints' => new NotBlank([
					'message' => 'Поле не может быть пустым.',
				]),
			])
			->add('password', PasswordType::class, [
				'label' => 'Пароль',
				'required' => false,
				'constraints' => new NotBlank([
					'message' => 'Поле не может быть пустым.',
				]),
			])
			->add('submit', SubmitType::class, [
				'label' => 'Войти',
			])
			->getForm();

		$form->handleRequest();

		$flashes = [];

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();

			$user = $this->model->getUser($data);

			$inputPass = $data['password'];
			$hash = $user['password'];

			if ( $user && password_verify($inputPass, $hash) ) {
				$this->session->set('auth', $user);
			} else {
				$flashes['danger'] = 'Неверные Имя пользователя или Пароль!';
				$this->session->set('flashes', $flashes);
			}

        	$redirect = $this->request->getPathInfo();

        	header("Location: $redirect");
        	exit;
	    }

	    if ($this->session->has('flashes')) {
	    	$flashes = $this->session->get('flashes');
	    	$this->session->remove('flashes');
	    }

		return $this->render('user/index.html.twig', [
			'form' => $form->createView(),
			'flashes' => $flashes,
		]);
	}

	public function logout()
	{
        if ($this->session->has('auth')) {
        	$this->session->remove('auth');
        }

    	$redirect = $this->request->headers->get('referer');
    	
    	header("Location: $redirect");
    	exit;
	}
}