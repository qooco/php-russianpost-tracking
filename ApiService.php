<?php

namespace bupy7\russianpost\tracking;

use SoapClient;
use bupy7\xml\constructor\XmlConstructor;

/**
 * PHP class library of tracking mailing via API Russian Post.
 * 
 * @author Belosludcev Vasilij <bupy765@gmail.com>
 * @see https://tracking.pochta.ru/specification
 * @see http://php.net/manual/ru/book.soap.php
 * @since 1.0.0
 */
class ApiService
{
    /**
     * URL to WSDL of Russian Post API.
     */
    const URL_WSDL = 'https://tracking.russianpost.ru/fc?wsdl';
    /**
     * URL to service of Russian Post API.
     */
    const URL_SERVICE = 'https://tracking.russianpost.ru/fc';
    /**
     * Language of response: russian.
     */
    const LANGUAGE_RUS = 'RUS';
    /**
     * Language of response: english.
     */
    const LANGUAGE_ENG = 'ENG';
    
    /**
     * @var string Login of access to Russian Post API.
     */
    protected $login;
    /**
     * @var string Password of access to Russian Post API.
     */
    protected $password;
    /**
     * @var string Language of response.
     */
    protected $language;
    
    /**
     * @param string $login
     * @param string $password
     * @param string $language
     */
    public function __construct($login, $password, $language = self::LANGUAGE_RUS)
    {
        $this->login = $login;
        $this->password = $password;
        $this->language = $language;
    }
    
    public function getTicket()
    {
        $in = [
            [
                'tag' => 'soapenv:Envelope',
                'attributes' => [
                    'xmlns:soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
                    'xmlns:pos' => 'http://fclient.russianpost.org/postserver',
                    'xmlns:fcl' => 'http://fclient.russianpost.org',
                ],
                'elements' => [
                    [
                        'tag' => 'soapenv:Header',
                    ],
                    [
                        'tag' => 'soapenv:Body',
                        'elements' => [
                            [
                                'tag' => 'pos:ticketRequest',
                                'elements' => [
                                    [
                                        'tag' => 'request',
                                        'elements' => [
                                            [
                                                'tag' => 'fcl:Item',
                                                'attributes' => [
                                                    'Barcode' => '1094449202104 4',
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'tag' => 'login',
                                        'content' => $this->login,
                                    ],
                                    [
                                        'tag' => 'password',
                                        'content' => $this->password,
                                    ],
                                    [
                                        'tag' => 'language',
                                        'content' => $this->language,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $xml = new XmlConstructor(['startDocument' => false]);
        $body = $xml->fromArray($in)->toOutput();     
        $this->sendRequest($body);
    }
    
    public function getResponseByTicket()
    {
    }
    
    protected function sendRequest($body)
    {
        $client = new SoapClient(self::URL_WSDL, ['trace' => 1, 'soap_version' => SOAP_1_1]);
        echo $client->__doRequest($body, self::URL_SERVICE, 'getTicket', SOAP_1_1);
    }
}
