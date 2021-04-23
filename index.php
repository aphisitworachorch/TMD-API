<?php
require "vendor/autoload.php";

use Dotenv\Dotenv;
use GuzzleHttp\Client;
class Weather {
    /**
     * @var Client
     */
    private $http;
    /**
     * @var string
     */
    private $bearer_token;

    public function env($env){
        return $_ENV[$env];
    }

    public function __construct ()
    {
        $this->env = Dotenv::createImmutable(__DIR__);
        $this->env->safeLoad ();
        $this->http = new Client(['base_uri'=>'https://data.tmd.go.th/nwpapi/v1/']);
        $this->bearer_token = $this->env('TMD_API_TOKEN');
    }

    public function getWeather($params,$queryString){
        try{
            return $this->http->request ('GET',$params,[
                'query'=>$queryString,
                'headers'=>[
                    'Accept'=>'application/json',
                    'Authorization'=>"Bearer {$this->bearer_token}"
                ]
            ])->getBody ()->getContents ();
        }catch(Exception $e){
            return json_encode(
                array("status"=>"error","message"=>$e->getMessage ())
            );
        }
    }

    /** Return or visual please use line chart */
    public function getWeatherByProvince(){
        /**
         * https://data.tmd.go.th/nwpapi/doc/apidoc/location/forecast_daily.html#%E0%B8%A3%E0%B8%B0%E0%B8%9A%E0%B8%B8%E0%B8%9E%E0%B8%B4%E0%B8%81%E0%B8%B1%E0%B8%94%E0%B8%A0%E0%B8%B9%E0%B8%A1%E0%B8%B4%E0%B8%A8%E0%B8%B2%E0%B8%AA%E0%B8%95%E0%B8%A3%E0%B9%8C
         */
        $provinceOptional = [
            'amphoe'=>'เมืองนครราชสีมา', /** อำเภอ */
            'tambon'=>'ตลาด',
            'subarea'=>0
        ];
        $queryString = [
            'province'=>'นครราชสีมา', /** จังหวัด */
            $provinceOptional,
            'fields'=>'tc_max,tc_min,cond,rh', /** ตัวแปรที่ https://data.tmd.go.th/nwpapi/doc/apidoc/location/forecast_daily.html */
            'date'=>'2021-04-24', /** วันเริ่มต้นการพยากรณ์อากาศ */
            'duration'=>10 /** จำนวนวันที่ต้องการ */
        ];
        return $this->getWeather ('forecast/location/daily/place',$queryString);
    }

    /** Return or visual please use geo map chart */
    public function getWeatherByArea(){
        /**
         * https://data.tmd.go.th/nwpapi/doc/apidoc/forecast_area.html#%E0%B8%A3%E0%B8%B0%E0%B8%9A%E0%B8%B8%E0%B8%8A%E0%B8%B7%E0%B9%88%E0%B8%AD%E0%B8%AA%E0%B8%96%E0%B8%B2%E0%B8%99%E0%B8%97%E0%B8%B5%E0%B9%88
         */
        $provinceOptional = [
            'amphoe'=>'เมืองนครราชสีมา', /** อำเภอ */
            'tambon'=>'ตลาด',
            'subarea'=>0
        ];
        $queryString = [
            'domain'=>1,
            'province'=>'นครราชสีมา', /** จังหวัด */
            $provinceOptional,
            'fields'=>'tc_max,tc_min,cond,rh', /** ตัวแปรที่ https://data.tmd.go.th/nwpapi/doc/apidoc/location/forecast_daily.html */
            'starttime'=>'2021-04-24T10:00:00' /** วันเริ่มต้นการพยากรณ์อากาศ */
        ];
        return $this->getWeather ('forecast/area/place',$queryString);
    }
}

$weather = new Weather();
print_r($weather->getWeatherByArea ());