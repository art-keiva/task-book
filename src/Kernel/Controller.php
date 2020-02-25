<?php

namespace App\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Extension\DebugExtension;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;

abstract class Controller
{
	public $request;
	public $model;
	public $formFactory;
	public $session;
	
	public function __construct(Request $request, string $controllerName)
	{
		$this->request = $request;
		$this->model = $this->loadModel($controllerName);
		$this->formFactory = $this->loadFormFactory();
		$this->session = $this->startSession();
	}

	public function render(string $template, array $params = [])
	{
		$twig = $this->getView();

		$params['request'] = $this->request;
		$params['session'] = $this->session;

		echo $twig->render($template, $params);
	}

	private function loadModel($controllerName)
	{
		$parts = explode('\\', $controllerName);
		$controller = end($parts);
		$alias = str_replace('Controller', '', $controller);
		$model = null;

		if (class_exists( $model = 'App\\Model\\' . ucfirst($alias) )) {
			$model = new $model;
		}

		return $model;
	}

	private function loadFormFactory()
	{
		return Forms::createFormFactoryBuilder()
			->addExtension(new ValidatorExtension(Validation::createValidator()))
			->getFormFactory();
	}

	private function startSession()
	{
		$sessionStorage = new NativeSessionStorage([], new NativeFileSessionHandler());

		$session = new Session($sessionStorage);

		$session->start();

		return $session;
	}

	private function getView()
	{
		/**
		 *	add symfony/form extension
		 **/
		$defaultFormTheme = 'bootstrap_4_layout.html.twig';

		$vendorDirectory = $_ENV['APP_DIR'] .'/vendor';

		$appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
		$vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

		$viewsDirectory = $_ENV['APP_DIR'] . '/templates';

		$loader = new FilesystemLoader([
			$viewsDirectory,
			$vendorTwigBridgeDirectory . '/Resources/views/Form',
		]);

		$twig = new Environment($loader, [
			'debug' => true,
			// 'cache' => false,
			// 'auto_reload' => true,
		]);

		$formEngine = new TwigRendererEngine([$defaultFormTheme], $twig);

		$twig->addRuntimeLoader(new FactoryRuntimeLoader([
		    FormRenderer::class => function () use ($formEngine) {
		        return new FormRenderer($formEngine);
		    },
		]));

		$twig->addExtension(new FormExtension());

		/**
		 *	add twig/debug extension
		 **/
		$twig->addExtension(new DebugExtension());

		/**
		 *	add symfony/translations extension, depends forms
		 **/
		$translator = new Translator('ru');

		$translator->addLoader('xlf', new XliffFileLoader());
		$translator->addResource(
		    'xlf',
		    $_ENV['APP_DIR'] .'/translations/messages.ru.xlf',
		    'en'
		);

		$twig->addExtension(new TranslationExtension($translator));

		/**
		 *	add symfony/validator translations, depends form & translations
		 **/
		$vendorFormDirectory = $vendorDirectory.'/symfony/form';
		$vendorValidatorDirectory = $vendorDirectory.'/symfony/validator';

		$translator->addResource(
		    'xlf',
		    $vendorFormDirectory.'/Resources/translations/validators.en.xlf',
		    'en',
		    'validators'
		);

		$translator->addResource(
		    'xlf',
		    $vendorValidatorDirectory.'/Resources/translations/validators.en.xlf',
		    'en',
		    'validators'
		);

		return $twig;
	}
}