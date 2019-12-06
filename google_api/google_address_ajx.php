<?php

namespace GoogleApi;

require_once 'Address.php';

use Exception;
use \GoogleApi\Address;

$action = trim($_GET['action']);
$address_string = trim($_GET['term']);
$address_id = isset($_GET['id']) ? $_GET['id'] : 0;
$token = isset($_GET['token']) ? $_GET['token'] : "";

try {
    $address = new Address();
    if ($action == "search") {
        $data = [];

        $response = $address->autoComplete($address_string, $token);
        foreach ($response->results as $res) {
            $data[] = ['id' => $res->id, 'label' => $res->address, 'value' => $res->address];
        }
        $output = ['results' => $data, 'token' => $response->token];
        echo json_encode($output);
    }

    if ($action == "select") {
        $output = $address->getAddressObject($address_string, $address_id, $token);
        echo json_encode($output);
    }
} catch (Exception $ex) {

    echo $ex->getMessage();
}
