<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

return function (App $app) {
    $container = $app->getContainer();

    // Tampilan Login
    $app->get('/login/', function (Request $request, Response $response, array $args) {

        // Render template dengan Twig
        return $this->view->render($response, 'content/auth/login.html');
    });

    // Tampilan Login
    $app->get('/register/', function (Request $request, Response $response, array $args) {

        // Render template dengan Twig
        return $this->view->render($response, 'content/auth/register.html');
    });

    // Tampilan Lupa Login
    $app->get('/forget/', function (Request $request, Response $response, array $args) {

        // Render template dengan Twig
        return $this->view->render($response, 'content/auth/forget_pass.html');
    });

    //POST Login
    $app->post("/login/", function (Request $request, Response $response) {

        $new_login = $request->getParsedBody();

        $username = trim(strip_tags($new_login['username']));
        $password = trim(strip_tags($new_login['password']));

        $sql = "SELECT id_pengguna_api, username, api_key FROM tbl_api_users WHERE username = :username AND password = :password";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":username" => $new_login["username"],
            ":password" => $new_login["password"],
        ];

        $stmt->execute($data);

        $user = $stmt->fetchObject();

        if (!$user) {

            return $response->withJson(["status" => "Gagal", "data" => "0"], 200);
        } else {

            $settings = $this->get('settings');
            $token = array(
                'id_pengguna_api' => $user->id_pengguna_api,
                'username' => $user->username
            );
            $token = JWT::encode($token, $settings['jwt']['secret'], "HS256");

            return $response->withJson(["status" => "Sukses", "Data" => $user, 'Token' => $token], 200);
        }
    });

    $app->post('/register/', function (Request $request, Response $response, array $args) {
        $input = $request->getParsedBody();
        $username = trim(strip_tags($input['username']));
        $email = trim(strip_tags($input['email']));
        $password = trim(strip_tags($input['password']));
        $api_key = trim(strip_tags($input['password'] . $input['password']));
        $hit = 0;
        $sql = "INSERT INTO tbl_api_users(username, email, password, api_key, hit) 
                VALUES(:username, :email, :password, :api_key, :hit)";

        if ($password == null) {
            return $response->withJson(['status' => 'error', 'Data yang Dimasukkan' => 'Password kosong.']);
        } else {
            $sth = $this->db->prepare($sql);
            $sth->bindParam("username", $username);
            $sth->bindParam("email", $email);
            $sth->bindParam("password", $password);
            $sth->bindParam("api_key", $api_key);
            $sth->bindParam("hit", $hit);
            $StatusInsert = $sth->execute();
            if ($StatusInsert) {
                $id_pengguna_api = $this->db->lastInsertId();
                $settings = $this->get('settings');
                $token = array(
                    'id_pengguna_api' =>  $id_pengguna_api,
                    'username' => $username
                );
                $token = JWT::encode($token, $settings['jwt']['secret'], "HS256");
                $dataUser = array(
                    'id_pengguna_api' => $id_pengguna_api,
                    'api_key' => $api_key,
                    'username' => $username
                );
                return $response->withJson(['status' => 'Sukses', 'Data Anda' => $dataUser, 'token' => $token]);
            } else {
                return $response->withJson(['status' => 'error', 'Data Anda' => 'error insert user.']);
            }
        }
    });
};
