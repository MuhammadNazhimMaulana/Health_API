<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    // Get ALL
    $app->get("/diseases/", function (Request $request, Response $response){
        $sql = "SELECT * FROM tbl_fake_illness";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["Status" => "Sukses", "Data Seluruh Penyakit" => $result], 200);
    });
    
    // Get One
    $app->get("/diseases/{id_illness}", function (Request $request, Response $response, $args){
        $id_illness = $args["id_illness"];
        $sql = "SELECT * FROM tbl_fake_illness WHERE id_illness = :id_illness";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_illness" => $id_illness]);
        $result = $stmt->fetch();
        return $response->withJson(["Status" => "Sukses", "Data Penyakit" => $result], 200);
    });

    //Search Penyakit
    $app->get("/diseases/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM tbl_fake_illness
                WHERE nama_penyakit LIKE '%$keyword%' OR gejala LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["Status" => "Sukses", "Data penyakit" => $result], 200);
    });

    // Post Penyakit
    $app->post("/diseases/", function (Request $request, Response $response) {

        $penyakit = $request->getParsedBody();

        $sql = "INSERT INTO tbl_fake_illness (nama_penyakit, gejala) VALUE (:nama_penyakit, :gejala)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":nama_penyakit" => $penyakit["nama_penyakit"],
            ":gejala" => $penyakit["gejala"],
        ];

        if ($data[":nama_penyakit"] == null || $data[":gejala"] == null) {

            return $response->withJson(["status" => "Gagal", "data" => "Tidak Boleh Kosong"], 200);

        } elseif ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Input Penyakit", "data" => "1"], 200);

        } else {

            return $response->withJson(["status" => "Gagal Input Penyakit", "data" => "0"], 200);
        }
    });

    // Update Penyakit
    $app->put("/diseases/{id_illness}", function (Request $request, Response $response, $args) {

        $id_illness = $args["id_illness"];
        $penyakit = $request->getParsedBody();

        $sql = "UPDATE tbl_fake_illness SET nama_penyakit = :nama_penyakit, gejala = :gejala WHERE id_illness = :id_illness";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_illness" => $id_illness,
            ":nama_penyakit" => $penyakit["nama_penyakit"],
            ":gejala" => $penyakit["gejala"],
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Update Penyakit", "data" => "1"], 200);

        } else {

            return $response->withJson(["status" => "Gagal Update Penyakit", "data" => "0"], 200);
        }
    });

    // Delete 1 Penyakit
    $app->delete("/diseases/{id_illness}", function (Request $request, Response $response, $args) {
        $id_illness = $args["id_illness"];
        $sql = "DELETE FROM tbl_fake_illness WHERE id_illness = :id_illness";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_illness" => $id_illness
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Data Penyakit", "data" => "1"], 200);

        } else {

            return $response->withJson(["status" => "Gagal Menghapus Data Penyakit", "data" => "0"], 200);
        }
    });

};
