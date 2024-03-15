<?php

namespace App\Libraries;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class OdkCentralRequest
{
    use ResponseTrait;

    public $api_url;
    private $token;
    private $client;

    /**
     * Init
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->api_url = env('odk.url');
        $auth = new OdkCentralAuth();
        $this->token = $auth->getAccessToken();
        $this->client = \Config\Services::curlrequest();
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * GET METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     */
    public function get(string $endpoint, array $params = [], array $headers = [])
    {
        return $this->requestHandler('get', $endpoint, $params, $headers);
    }

    /**
     * GET RAW METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     */
    public function getBody(string $endpoint, array $params = [], array $headers = [])
    {
        return $this->requestHandler('get', $endpoint, $params, $headers, true);
    }

    /**
     * POST METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @param object $file
     */
    public function post(string $endpoint, array $params = [], array $headers = [], $file = null)
    {
        return $this->requestHandler('post', $endpoint, $params, $headers, $file);
    }

    /**
     * PATCH METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     */
    public function patch(string $endpoint, array $params = [], array $headers = [])
    {
        return $this->requestHandler('patch', $endpoint, $params, $headers);
    }

    /**
     * PUT METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     */
    public function put(string $endpoint, $params, array $headers = [])
    {
        return $this->requestHandler('put', $endpoint, $params, $headers);
    }

    /**
     * DELETE METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     */
    public function delete(string $endpoint, array $params = [], array $headers = [])
    {
        return $this->requestHandler('delete', $endpoint, $params, $headers);
    }

    /**
     * DOWNLOAD METHOD
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     */
    public function download(string $endpoint, array $params = [], array $headers = [])
    {
        return $this->requestHandler('get', $endpoint, $params, $headers, true, true);
    }

    /**
     * Handle all request types
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @param bool $raw
     * @param bool $download
     */
    private function requestHandler(
        string $method,
        string $endpoint,
        array $params = [],
        array $headers = [],
        bool $raw = false,
        bool $download = false
    ) {
        try {
            $this->client->setHeader("Authorization", "Bearer " . $this->token);
            $response = $this->client->{$method}($this->api_url . $endpoint, $params, $headers);

            if ($download) {
                return $this->response->setStatusCode($response->getStatusCode())
                    ->setContentType($response->getHeaderLine('Content-Type'))
                    ->setBody($response->getBody())
                    ->send();
            }

            if ($raw) {
                return $response->getBody();
            }

            $body = $response->getBody();

            if (empty($body)) {
                return [];
            }

            return json_decode($body, true);

        } catch (\Exception $exception) {
            return $this->getResponse(
                ['status' => 500, 'error' => $exception->getMessage()],
                false
            );
        }
    }
}
