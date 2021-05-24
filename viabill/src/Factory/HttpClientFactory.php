<?php
/**
* NOTICE OF LICENSE
*
* @author    Written for or by ViaBill
* @copyright Copyright (c) Viabill
* @license   Addons PrestaShop license limitation
* @see       /LICENSE
*
*
*/

namespace ViaBill\Factory;

use Guzzle\Http\Client;
use ViaBill\Config\Config;

/**
 * Class HttpClientFactory
 *
 * @package ViaBill\Factory
 */
class HttpClientFactory
{
    /**
     * Config Variable Declaration.
     *
     * @var Config
     */
    private $config;

    /**
     * HttpClientFactory constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Gets Guzzle HTTP Client.
     *
     * @return Client
     */
    public function getClient()
    {
        $config = array(
            'request.options' => array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ),
            ),
        );

        return new Client($this->config->getBaseUrl(), $config);
    }
}
