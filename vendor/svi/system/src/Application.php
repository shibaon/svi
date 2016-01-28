<?php

namespace Svi;

use \Symfony\Component\Debug\ErrorHandler;
use \Symfony\Component\Debug\ExceptionHandler;
use \Silex\Provider\DoctrineServiceProvider;
use \Silex\Provider\ServiceControllerServiceProvider;
use \Silex\Provider\TwigServiceProvider;

class Application
{
	private static $_instance;

	/**
	 * @var Logger
	 */
	private $logger;

	private $console;
	/**
	 * @var \Silex\Application
	 */
	private $silex;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var Bundles
	 */
	private $bundles;

	/**
	 * @var Routing
	 */
	private $routing;

	private function __construct(array $argv = null)
	{
		if ($argv) {
			$this->console = true;
		}

		umask(0000);
		$loader = require $this->getRootDir() . '/vendor/autoload.php';
		$this->silex = new \Silex\Application();

		$loader->add('', $this->getRootDir() . '/src');

		require_once $this->getRootDir() . '/app/config/config.php';
		$this->config = Config::getInstance($this);
		$this->silex['debug'] = $this->config->get('debug');

		$this->logger = Logger::getInstance($this);

		ErrorHandler::register();
		$handler = ExceptionHandler::register($this->config->get('debug'));
		$handler->setHandler([$this->logger, 'handleException']);

		if ($this->config->get('db')) {
			$this->silex->register(new DoctrineServiceProvider(), [
				'db.options' => $this->config->get('db'),
			]);
			Entity::$connection = $this->silex['db'];
		}
		$this->tryInitTwig();
		if (!$this->console) {
			$this->silex['session'] = $this->silex->share(function(){
				return Session::getInstance($this);
			});

			$this->silex['cookies'] = $this->silex->share(function(){
				return Cookies::getInstance($this);
			});
		}
		$this->silex['translation'] = $this->silex->share(function(){
			return Translation::getInstance($this);
		});

		$this->bundles = Bundles::getInstance($this);

		if (!$this->console) {
			$this->silex->register(new ServiceControllerServiceProvider());
		}
		$this->routing = Routing::getInstance($this);

		if ($this->console) {
			$this->console = Console::getInstance($this, $argv);
		}
	}

	protected function _run()
	{
		if (!$this->console) {
			$this->getSilex()->run();
		} else {
			$this->console->run();
		}
	}

	private function __clone(){}
	private function __wakeup(){}

	static public function run(array $argv = null)
	{
		if (self::$_instance === null) {
			self::$_instance = new self($argv);
			self::$_instance->_run();
		} else {
			throw new \Exception('Application already run');
		}
	}

	public function tryInitTwig()
	{
		if ($this->getConfig()->get('twig')) {
			$this->silex->register(new TwigServiceProvider(), [
				'twig.path' => [
					$this->getRootDir() . '/src',
					$this->getRootDir() . '/vendor',
				],
				'twig.options' => [
					'cache' => $this->silex['debug'] ? false : $this->getRootDir() . '/app/cache',
				] + $this->getConfig()->get('twig'),
			]);
			$this->getSilex()['twig']->addExtension(new SilexTwigExtension($this));
		}
	}

	/**
	 * @return Config
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @return Routing
	 */
	public function getRouting()
	{
		return $this->routing;
	}

	/**
	 * @return Bundles
	 */
	public function getBundles()
	{
		return $this->bundles;
	}

	/**
	 * @return \Silex\Application
	 */
	public function getSilex()
	{
		return $this->silex;
	}

	public function get($service)
	{
		return $this->silex[$service];
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	public function getRequest()
	{
		return $this->silex['request'];
	}

	/**
	 * @return Session
	 */
	public function getSession()
	{
		return $this->get('session');
	}

	/**
	 * @return Cookies
	 */
	public function getCookies()
	{
		return $this->get('cookies');
	}

	/**
	 * @return Translation
	 */
	public function getTranslation()
	{
		return $this->get('translation');
	}

	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getDb()
	{
		return $this->silex['db'];
	}

	/**
	 * @return \Svi\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @return string Always returns current site dir with "/" in the end, so like /var/www/sample/www/sample.com/ will be returned
	 */
	public function getRootDir()
	{
		return dirname(dirname(dirname(dirname(__DIR__))));
	}

	/**
	 * @return \Twig_Environment
	 */
	public function getTwig()
	{
		return $this->get('twig');
	}

	public function isConsole()
	{
		return $this->console;
	}

}