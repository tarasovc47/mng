<?php
namespace common\components;

use yii\base\Component;

class Dadata extends Component{
    private $apiKey;
    private $secretKey;

	public function __construct($apiKey, $secretKey) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
    }
    public function clean($type, $data) {
        $requestData = array($data);
        return $this->executeRequest("https://dadata.ru/api/v2/clean/$type", $requestData);
    }
    public function cleanRecord($structure, $record) {
        $requestData = array(
          "structure" => $structure,
          "data" => array($record)
        );
        return $this->executeRequest("https://dadata.ru/api/v2/clean", $requestData);
    }
    private function prepareRequest($curl, $data) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
             'Accept: application/json',
             'Authorization: Token ' . $this->apiKey,
             'X-Secret: ' . $this->secretKey,
          ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    private function executeRequest($url, $data) {
        $result = false;
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl, $data);
            $result = curl_exec($curl);
            $result = json_decode($result, true);
            curl_close($curl);
        }
        return $result;
    }

    public function suggest($type, $fields)
    {
        $result = false;
        if ($ch = curl_init("http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/$type"))
        {
             curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                 'Content-Type: application/json',
                 'Accept: application/json',
                 'Authorization: Token '.$this->apiKey
              ));
             curl_setopt($ch, CURLOPT_POST, 1);
             // json_encode
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
             $result = curl_exec($ch);
             $result = json_decode($result, true);
             curl_close($ch);
        }
        return $result;
    }
}
