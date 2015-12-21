<?php

namespace bupy7\russianpost\tracking;

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
}
