<?php

  Namespace PiDisplay;

  use \PiDisplay\Controllers\Controller;
  use \PiDisplay\System\Settings;
  use \PiDisplay\Routes\WebRoutes;
  use \PiDisplay\System\Database as DB;
  use \Dotenv\Dotenv;
  use \Slim\Views\Twig;
  use \Slim\Views\TwigExtension;
  use \Slim\Views\TwigMiddleware;
  use \Slim\Factory\AppFactory;
  use \DI\Container;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  require '../../vendor/autoload.php';

  class PiDisplay {
    /**
     * Loads all the important essential variables
     * @return Void
     */
    private function loadEssentials() {
      session_start();
      date_default_timezone_set('UTC');
      spl_autoload_register('self::classAutoloader');

      $dotenv = Dotenv::createImmutable(__DIR__, '../.env');
      $dotenv->load();

      DB::createInstance();

      define('DEVELOPMENT_MODE', filter_var(getenv('DEV_MODE'), FILTER_VALIDATE_BOOLEAN));
      Settings::devMode();
    }

    /**
     * This will require a class automatically
     * @param  Class $class The needed class
     */
    private static function classAutoloader($class) {
      $class = str_replace("PiDisplay\\", "", $class);
      $class = str_replace("\\", "/", $class);
      $class = "../".$class.".php";
      require_once($class);
    }

    /**
     * Starts the PiDisplay API Service
     * @return Void
     */
    public static function start() {
      //Load Essential variables
      self::loadEssentials();
      // Create Container
      $container = new Container();
      AppFactory::setContainer($container);
      // Set view in Container
      $container->set('view', function() {
          return new Twig('../View', [ 'cache' => (DEVELOPMENT_MODE) ? false : '../View/Cache' ]);
      });
      //Create App
      $app = AppFactory::create();
      //Add some twig middleware
      $app->add(TwigMiddleware::createFromContainer($app));
      $app->add($app->addErrorMiddleware(true, true, true));
      //If member session exists sign in
      if(isset($_SESSION['MEMBER'])) {
        $view->getEnvironment()->addGlobal('user', $_SESSION['MEMBER']);
      }
      //Start an instance of controller and routing
      Controller::createInstance($app);
      WebRoutes::start($app);
      //Start the app
      $app->run();
    }
  }

  /**
   * Starts Everything
   * @var PiDisplay
   */
  PiDisplay::start();
