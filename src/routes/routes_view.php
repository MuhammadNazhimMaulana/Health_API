<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    // $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
    //     // Sample log message
    //     $container->get('logger')->info("Slim-Skeleton '/' route");

    //     // Render index view
    //     return $container->get('renderer')->render($response, 'index.phtml', $args);
    // });

    // Testing Twig
    $app->get('/', function (Request $request, Response $response, array $args) {

        $data["name"] = "Bonevian";

        // Render template dengan Twig
        return $this->view->render($response, 'content/home.html', $data);
    });

    // Halaman Getting Start
    $app->get('/getting-start/', function (Request $request, Response $response, array $args) {

        $data["name"] = "Bonevian";

        // Render template dengan Twig
        return $this->view->render($response, 'content/start.html', $data);
    });

    $app->get('/testing/', function (Request $request, Response $response, array $args) {

        // Render template dengan Twig
        return $this->view->render($response, 'content/input.html');
    });

    $app->post('/testing/', function (Request $request, Response $response, array $args) {
        $input = $request->getParsedBody();
        $username = trim(strip_tags($input['username']));
    
    
        return $response->withJson(['status' => 'Sukses', 'Data Anda' => $username]);
    });

};
