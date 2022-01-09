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

    // Get ALL
    $app->get("/medicines/", function (Request $request, Response $response){
        $sql = "SELECT * FROM tbl_list_obat";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["Status" => "Sukses", "Data_Seluruh_Obat" => $result], 200);
    });
    
    // Get One
    $app->get("/medicines/{id_list}", function (Request $request, Response $response, $args){
        $id_list = $args["id_list"];
        $sql = "SELECT * FROM tbl_list_obat WHERE id_list_obat = :id_list";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id_list" => $id_list]);
        $result = $stmt->fetch();
        return $response->withJson(["Status" => "Sukses", "Data_Obat" => $result], 200);
    });

    //Search Obat
    $app->get("/medicines/search/", function (Request $request, Response $response, $args) {
        $keyword = $request->getQueryParam("keyword");
        $sql = "SELECT * FROM tbl_list_obat
                WHERE nama_obat LIKE '%$keyword%' OR jumlah_stok LIKE '%$keyword%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["Status" => "Sukses", "Data_Obat" => $result], 200);
    });

    // Post Obat
    $app->post("/medicines/", function (Request $request, Response $response) {

        $obat_baru = $request->getParsedBody();

        $sql = "INSERT INTO tbl_list_obat (nama_obat, jumlah_stok, tanggal_restock) VALUE (:nama_obat, :jumlah_stok, :tanggal_restock)";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":nama_obat" => $obat_baru["nama_obat"],
            ":jumlah_stok" => $obat_baru["jumlah_stok"],
            ":tanggal_restock" => $obat_baru["tanggal_restock"],
        ];

        if ($data[":nama_obat"] == null || $data[":jumlah_stok"] == null) {

            return $response->withJson(["status" => "Gagal", "data" => "Tidak Boleh Kosong"], 200);

        } elseif ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Input Obat", "data" => "1"], 200);

        } else {

            return $response->withJson(["status" => "Gagal Input Obat", "data" => "0"], 200);
        }
    });

    // Update Obat
    $app->put("/medicines/{id_list}", function (Request $request, Response $response, $args) {

        $id_list = $args["id_list"];
        $perbarui_obat = $request->getParsedBody();

        $sql = "UPDATE tbl_list_obat SET nama_obat = :nama_obat, jumlah_stok = :jumlah_stok, tanggal_restock = :tanggal_restock WHERE id_list_obat = :id_list";

        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_list" => $id_list,
            ":nama_obat" => $perbarui_obat["nama_obat"],
            ":jumlah_stok" => $perbarui_obat["jumlah_stok"],
            ":tanggal_restock" => $perbarui_obat["tanggal_restock"],
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Update Obat", "data" => "1"], 200);

        } else {

            return $response->withJson(["status" => "Gagal Update Obat", "data" => "0"], 200);
        }
    });

    // Delete 1 Obat
    $app->delete("/medicines/{id_list}", function (Request $request, Response $response, $args) {
        $id_list = $args["id_list"];
        $sql = "DELETE FROM tbl_list_obat WHERE id_list_obat = :id_list";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_list" => $id_list
        ];

        if ($stmt->execute($data)) {

            return $response->withJson(["status" => "Sukses Menghapus Data Obat", "data" => "1"], 200);

        } else {

            return $response->withJson(["status" => "Gagal Menghapus Data Obat", "data" => "0"], 200);
        }
    });

};
