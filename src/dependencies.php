<?php

use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

return function (App $app) {
    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // Database
    $container['db'] = function ($c) {
        $settings = $c->get('settings')['db'];
        $server = $settings['driver'] . ":host=" . $settings['host'] . ";dbname=" . $settings['dbname'];
        $conn = new PDO($server, $settings['user'], $settings['pass']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    };

    // Twig
    $container['view'] = function($c){
        $view = new \Slim\Views\Twig('../templates');
    
        // Instantiate and add Slim specific extension
        $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
        $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
    
        return $view;
    };

};
