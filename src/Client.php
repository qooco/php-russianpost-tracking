<?php

namespace vertx\russianpost\tracking;

use \SoapClient;
use \SoapFault;
use \SoapParam;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LoggerAwareInterface;

class Client implements LoggerAwareInterface
{
    /**
     * URL to WSDL of Russian Post API.
     */
    const URL_WSDL = 'https://tracking.russianpost.ru/rtm34?wsdl';
    /**
     * URL to service of Russian Post API.
     */
    const URL_SERVICE = 'https://tracking.russianpost.ru/rtm34';
    /**
     * Language of response: russian.
     */
    const LANGUAGE_RUS = 'RUS';
    /**
     * Language of response: english.
     */
    const LANGUAGE_ENG = 'ENG';
    /**
     * Type of message: mail
     */
    const MESSAGE_TYPE_MAIL = 0;
    /**
     * Type of message: mail order
     */
    const MESSAGE_TYPE_ORDER = 1;
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
     * @var string Type of message.
     */
    protected $messageType;
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
     * @param int $messageType
     * @param string $language
     */
    public function __construct($login, $password, $messageType = self::MESSAGE_TYPE_MAIL, $language = self::LANGUAGE_RUS)
    {
        $this->login = $login;
        $this->password = $password;
        $this->language = $language;
        $this->messageType = $messageType;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function getOperationHistory($code)
    {
        $body = [
            'getOperationHistory' => [
                'OperationHistoryRequest' => [
                    'Barcode' => $code,
                    'Language' => $this->language,
                    'MessageType' => $this->messageType
                ],
                'AuthorizationHeader' => [
                    'mustUnderstand' => 1,
                    'login' => $this->login,
                    'password' => $this->password
                ]
            ]
        ];

        return $this->sendRequest('getOperationHistory', $body);
    }

    public function postalOrderEventsForMail($code)
    {
        $body = [
            'PostalOrderEventsForMail' => [
                'AuthorizationHeader' => [
                    'login' => $this->login,
                    'password' => $this->password
                ],
                'PostalOrderEventsForMailInput' => [
                    'Barcode' => $code,
                    'Language' => $this->language
                ]
            ]
        ];

        return $this->sendRequest('PostalOrderEventsForMail', $body);
    }

    protected function sendRequest($command, $params, $trace = 1)
    {
        $response = '';

        $client = new SoapClient(self::URL_WSDL, [
            'trace' => $trace,
            'soap_version' => SOAP_1_2
            ]
        );

        $paramName = array_keys($params)[0];
        $soapParams = new SoapParam($params[$paramName], $paramName);

        try {
            $response = $client->$command($soapParams);
        } catch (SoapFault $fault) {
            $this->errorMessage = $fault;
        }

        if($this->logger) {
            $this->logger = [
                'req' => $client->__getLastRequest(),
                'resp' => $client->__getLastResponse()
            ];
        }

        return $response;
    }

    public static function parseOperationHistory($data) {
        return json_decode(json_encode($data), 'assoc');
    }
        
    public function getErrorMessage() {
        return $this->errorMessage;   
    }
}
