<?php

namespace App\Http\Helpers;

use Carbon\Carbon;
use SimpleXMLElement;


class IPGCharging
{
    function __construct()
    {
    }

    public function loginRequest()
    {

        $loginpayload = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soap="http://www.4cgroup.co.za/soapauth" xmlns:gen="http://www.4cgroup.co.za/genericsoap">
                        <soapenv:Header>
                            <soap:Token>?</soap:Token>
                            <soap:EventID>2500</soap:EventID>
                        </soapenv:Header>
                        <soapenv:Body>
                            <gen:getGenericResult>
                                <Request>
                                    <!--Zero or more repetitions:-->
                                    <dataItem>
                                    <name>Username</name>
                                        <type>String</type>
                                            <value>921465</value>
                                    </dataItem>
                                    <dataItem>
                                    <name>Password</name>
                                        <type>String</type>
                                            <value>jdL^016snQ@K</value>
                                    </dataItem>
                                </Request>
                            </gen:getGenericResult>
                        </soapenv:Body>
                        </soapenv:Envelope>';

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->post('https://41.217.203.61:30010/iPG/b2c/ussd_push?wsdl', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'text/xml',
                    'accept" => "*/*',
                ],
                'body' => $loginpayload
            ]);
            $result = $response->getBody()->getContents();

            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
            $xml = new SimpleXMLElement($response);
            $body = $xml->xpath('//SOAPAPIResult')[0];

            $data = json_encode($body);
            $jdatason = json_decode($data, true);
            return $jdatason['response']['dataItem']['value'];

        } catch (\Throwable $th) {

            return $th->getMessage();
        }

    }
}
