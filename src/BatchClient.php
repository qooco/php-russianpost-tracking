<?php

namespace vertx\russianpost\tracking;

use \SoapClient;
use \SoapFault;
use \SoapParam;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LoggerAwareInterface;

class BatchClient implements LoggerAwareInterface
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
     * @var string Error code.
     */
    protected $errorCode;

    /**
     * @var string Error text information.
     */
    protected $errorMessage;

    /**
     * @var \Psr\Log\LoggerInterface PSR compatible logger.
     */
    public $logger;

    
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

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function getTicket(array $codes)
    {
        $body = [
            'ticketRequest' => [
                'request' => array(
                    'Item' => $this->renderBarcodes($codes)
                ),
                'language' => $this->language,
                'login' => $this->login,
                'password' => $this->password
            ]
        ];

        return $this->sendRequest('getTicket', $body);
    }

    public function getResponseByTicket($ticket)
    {
        $body = [
            'answerByTicketRequest' => [
                'ticket' => $ticket,
                'login' => $this->login,
                'password' => $this->password
            ]
        ];

        return $this->sendRequest('getResponseByTicket', $body);
    }

    private function renderBarcodes(array $codes) {
        $items = '';
        foreach ($codes as $code) {
            $items[] = array(
                'Barcode' => $code
            );
        }

        return $items;
    }

    protected function sendRequest($command, $params, $trace = 1)
    {
        $client = new SoapClient(self::URL_WSDL, [
            'trace' => $trace,
            'soap_version' => SOAP_1_1
            ]
        );

        $paramName = array_keys($params)[0];
        $soapParams = new SoapParam($params[$paramName], $paramName);

        $return = null;

        try {
            $response = $client->$command($soapParams);
            $return = $response->value;
        } catch (SoapFault $fault) {
            $this->errorMessage = $fault;
        }

        if($this->logger) {
            $this->logger = [
                'req' => $client->__getLastRequest(),
                'resp' => $client->__getLastResponse()
            ];
        }

        return $return;
    }

    public static function parseItems($data) {
        return json_decode(json_encode($data), 'assoc');
    }
}
