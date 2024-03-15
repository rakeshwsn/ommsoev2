<?php

namespace App\Libraries;

use Config\Services;

class OdkCentral
{
    private OdkCentralRequest $api;
    private ?string $projectId = null;
    private ?string $xmlFormId = null;
    private ?string $submissionId = null;
    private string $endpoint = '';
    private array $params = [];
    private array $headers = [];
    private ?object $file = null;

    public function __construct()
    {
        $this->api = new OdkCentralRequest;
    }

    /**
     * Set the users endpoint.
     *
     * @param string|int $q
     * @return $this
     */
    public function users(?string $q = null): self
    {
        $endpoint = '/users';
        if (is_int($q)) {
            $endpoint .= '/' . $q;
        }
        $this->endpoint = $endpoint;
        $this->params = [
            'q' => $q,
        ];

        return $this;
    }

    /**
     * Set the app-users endpoint.
     *
     * @param string|int $q
     * @return $this
     */
    public function appUsers(?string $q = null): self
    {
        $this->headers = [
            'X-Extended-Metadata' => 'true',
        ];

        $endpoint = '/app-users';
        if (is_int($q)) {
            $endpoint .= '/' . $q;
        }
        $this->endpoint = $endpoint;
        $this->params = [
            'q' => $q,
        ];

        return $this;
    }

    /**
     * Set the roles endpoint.
     *
     * @param string|int $q
     * @return $this
     */
    public function roles(?string $q = null): self
    {
        $endpoint = '/roles';
        if (is_int($q)) {
            $endpoint .= '/' . $q;
        }
        $this->endpoint = $endpoint;
        $this->params = [
            'q' => $q,
        ];

        return $this;
    }

    /**
     * Set the assignements endpoint.
     *
     * @param int $id
     * @return $this
     */
    public function assignements(?int $id = null): self
    {
        $this->headers = [
            'X-Extended-Metadata' => 'true',
        ];

        $endpoint = '/assignements';
        if (is_int($id)) {
            $endpoint .= '/' . $id;
        }
        $this->endpoint = $endpoint;
        $this->params = [
            'id' => $id,
        ];

        return $this;
    }

    /**
     * Set the submissions endpoint.
     *
     * @param string|int $id
     * @return $this
     */
    public function submissions(?string $id = null): self
    {
        $formEndpoint = '/projects/' . ($this->projectId ?? '') . '/forms/' . ($this->xmlFormId ?? '');
        $submissionsEndpoint = ($this->submissionId) ? '/submissions/' . $this->submissionId : '/submissions';
        $this->endpoint = $formEndpoint . $submissionsEndpoint;
        $this->params = [
            'id' => $id,
        ];

        return $this;
    }

    // ... other methods

    /**
     * Create a new get request.
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return collection
     */
    public function get(string $endpoint = '', array $params = [], array $headers = []): \Psr\Http\Message\ResponseInterface
    {
        $this->endpoint = $endpoint;
        $this->params = $params;
        $this->headers = $headers;

        return $this->api->get($this->endpoint, $this->params, $this->headers);
    }

    // ... other methods
}
