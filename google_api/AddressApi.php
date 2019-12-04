<?php

namespace GoogleApi;

require_once '../vendor/autoload.php';

use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\GuzzleException;
use Ramsey\Uuid\Uuid as Uuid;


class Address
{

    private static $API_KEY = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    private static $GOOGLE_URL = "https://maps.googleapis.com/maps/api";
    private static $conn;
    private static $MAX_RESULTS = 5;
    public $GEOCODE = false;
    private static $client;
    private static $country = "au";

    public function __construct()
    {
        self::$client = new Client();
    }

    private static function get($request, $data = [])
    {
        try {
            $url = self::$GOOGLE_URL . "&key=" .  self::$API_KEY;
            $res = self::$client->request('GET', $url, [
                'timeout' => 60,
                'headers' => ['Content-Type' => 'application/json'],
                'query' => $data
            ]);
            return json_decode($res->getBody(), true);
        } catch (GuzzleException $ex) {
            return ['success' => false, 'message' => "Excetion:" . $ex->getMessage()];
        }
    }

    function getAddressObject($address_string, $address_id, $token = null)
    {
        $parts = [];
        $fields = "&fields=address_component";
        if ($this->GEOCODE) {
            $fields = $fields . ",geometry";
        }
        if ($token === "") {
            $token = $this->generateToken();
        }
        $request = self::$GOOGLE_URL . "/place/details/json?&placeid=" . $address_id . "&sessiontoken=" . $token . $fields;
        $results = json_decode(self::get($request));
        if (!empty($results->result)) {
            foreach ($results->result->address_components as $comp) {
                if (in_array('floor', $comp->types)) {
                    $parts['LevelNumber'] = strtoupper($comp->short_name);
                }
                if (in_array('route', $comp->types)) {
                    $parts['Street'] = strtoupper($comp->short_name);
                }
                if (in_array('street_number', $comp->types)) {
                    $parts['Number'] = strtoupper($comp->short_name);
                }
                if (in_array('locality', $comp->types)) {
                    $parts['Suburb'] = strtoupper($comp->long_name);
                }
                if (in_array('administrative_area_level_1', $comp->types)) {
                    $parts['State'] = strtoupper($comp->short_name);
                }
                if (in_array('postal_code', $comp->types)) {
                    $parts['Postcode'] = $comp->short_name;
                }
                if (in_array('country', $comp->types)) {
                    $parts['Country'] = strtoupper($comp->long_name);
                }
            }
            if ($this->GEOCODE) {
                if ($location = $results->result->geometry->location) {
                    $parts['Latitude'] = $location->lat;
                    $parts['Longitude'] = $location->lng;
                }
            }
            $parts['StreetLine'] = $parts['Number'] . " " . $parts['Street'];
        }
        return json_decode(json_encode($parts));
    }

    public function autoComplete($term, $token = null)
    {
        $matches = [];
        if ($token === "") {
            $token = $this->generateToken();
        }
        $request = "/place/autocomplete/json?input=" . urlencode($term) . "&sessiontoken=" . $token . "&components=country:" . self::$country . "&types=address";
        $results = json_decode(self::$conn->get($request));
        if ($results) {
            foreach ($results->predictions as $result) {
                $matches[] = ['id' => $result->place_id, 'address' => $result->description];
            }
        }
        $output = ['term' => $term, 'results' => $matches, 'token' => $token];
        return json_decode(json_encode($output));
    }

    private function generateToken()
    {
        $uuid4 = Uuid::uuid4();
        return $uuid4->toString();
    }
}
