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

namespace ViaBill\Service\Api;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\CurlException;
use ViaBill\Adapter\Tools;
use ViaBill\Factory\HttpClientFactory;
use ViaBill\Object\Api\ApiResponse;
use ViaBill\Object\Api\ApiResponseError;

/**
 * Class ApiRequest
 *
 * @package ViaBill\Service\Api
 */
class ApiRequest
{
    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * HTTP Client Factory Variable Declaration.
     *
     * @var HttpClientFactory
     */
    private $clientFactory;

    /**
     * Tools Variable Declaration.
     *
     * @var Tools
     */
    private $tools;

    /**
     * ApiRequest constructor.
     *
     * @param \ViaBill $module
     * @param HttpClientFactory $clientFactory
     * @param Tools $tools
     */
    public function __construct(\ViaBill $module, HttpClientFactory $clientFactory, Tools $tools)
    {
        $this->module = $module;
        $this->clientFactory = $clientFactory;
        $this->tools = $tools;
    }

    /**
     * API Request Post Method.
     *
     * @param string $url
     * @param array $params
     * @return ApiResponse|null
     */
    public function post($url, $params = array())
    {
        $response = null;
        $body = '';
        $errors = array();
        $effectiveUrl = '';

        try {
            $response = $this->clientFactory->getClient()->post($url, null, $params['body'])->send();

            if ($response->getBody()) {
                $body = $response->getBody()->__toString();
            }

            $statusCode = $response->getStatusCode();
            $effectiveUrl = $response->getEffectiveUrl();
        } catch (ClientErrorResponseException $clientException) {
            $errorBody = $clientException->getResponse()->getBody() ?
                $clientException->getResponse()->getBody()->__toString() :
                '';

            $statusCode = $clientException->getCode();
            $errors = $this->getResponseErrors($errorBody, $clientException->getMessage());
        } catch (CurlException $requestException) {
            $statusCode = $requestException->getCode();
            $errors = $this->getResponseErrors(
                '',
                $this->module->l('ViaBill service is down at the moment. Please wait and refresh the page or contact ViaBill support at merchants@viabill.com')
            );
        } catch (\Exception $exception) {
            $statusCode = $exception->getCode();
            $errors = $this->getResponseErrors('', $exception->getMessage());
        }

        return new ApiResponse($statusCode, $body, $errors, $effectiveUrl);
    }

    /**
     * API Request Get Method.
     *
     * @param string $url
     * @param array $options
     *
     * @return ApiResponse
     */
    public function get($url, $options = array())
    {
        $response = null;
        $body = '';
        $errors = array();

        try {
            $response = $this->clientFactory->getClient()->get($url, null, $options)->send();

            if ($response->getBody()) {
                $body = $response->getBody()->__toString();
            }

            $statusCode = $response->getStatusCode();
        } catch (ClientErrorResponseException $clientException) {
            $errorBody = $clientException->getResponse()->getBody() ?
                $clientException->getResponse()->getBody()->__toString() :
                '';

            $statusCode = $clientException->getCode();
            $errors = $this->getResponseErrors($errorBody, $clientException->getMessage());
        } catch (CurlException $requestException) {
            $statusCode = $requestException->getCode();
            $errors = $this->getResponseErrors(
                '',
                $this->module->l('ViaBill service is down at the moment. Please wait and refresh the page or contact ViaBill support at merchants@viabill.com')
            );
        } catch (\Exception $exception) {
            $statusCode = $exception->getCode();
            $errors = $this->getResponseErrors('', $exception->getMessage());
        }

        return new ApiResponse($statusCode, $body, $errors);
    }

    /**
     * Gets API Request Response Errors.
     *
     * @param string $body
     * @param string $exceptionMessage
     *
     * @return array
     */
    private function getResponseErrors($body, $exceptionMessage)
    {
        $normalizedBody = json_decode($body, true);
        if (empty($normalizedBody['errors']) || !isset($normalizedBody['errors'])) {
            return array(new ApiResponseError('', $exceptionMessage));
        }
        $errors = $normalizedBody['errors'];
        $result = array();

        foreach ($errors as $error) {
            $result[] = new ApiResponseError(
                (string) $error['field'],
                (string) $error['error']
            );
        }

        return $result;
    }
}
