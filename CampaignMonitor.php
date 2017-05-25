<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Exception\UnexpectedApiResponseException;

/**
 * ConstantContact OAuth2 provider adapter.
 */
class CampaignMonitor extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.createsend.com/api/v3.1/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://api.createsend.com/oauth';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://api.createsend.com/oauth/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://www.campaignmonitor.com/api/';

    /**
     * Campaign monitor require 'type' query parameter with value 'web_server' to be appended to the authorization URL.
     * @see https://www.campaignmonitor.com/api/getting-started/#authenticating-with-oauth
     *
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->AuthorizeUrlParameters = [
            'response_type' => 'code',
            'type' => 'web_server',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callback,
            'scope' => $this->scope,
        ];

        $this->tokenExchangeParameters = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->callback
        ];

        $this->tokenRefreshParameters = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->getStoredData('refresh_token'),
        ];

        /** Campaign monitor explicitly require access token to be set as Bearer.  */

        // if access token is found in storage, utilize else
        if (!empty($this->getStoredData('access_token'))) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $this->getStoredData('access_token')
            ];
        }

        // use the one supplied in config.
        if (!empty($this->config->get('access_token'))) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $this->config->get('access_token')
            ];
        }
    }

    public function getClients()
    {
        return $this->apiRequest('clients.json', 'GET');
    }

    public function getEmailList($cliend_id)
    {
        if (empty($cliend_id)) {
            throw new InvalidArgumentException('Client ID is missing');
        }

        return $this->apiRequest("clients/$cliend_id/lists.json");
    }

    public function addSubscriber($list_id, $payload = [])
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        if (empty($payload)) {
            throw new InvalidArgumentException('Payload is missing');
        }

        $headers = ['Content-Type' => 'application/json'];

        return $this->apiRequest("subscribers/$list_id.json", 'POST', $payload, $headers);
    }

    public function addSubscriberEmailName($list_id, $email, $name)
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

        if (empty($name)) {
            throw new InvalidArgumentException('Name is missing');
        }

        $payload = [
            "EmailAddress" => $email,
            "Name" => $name,
            "CustomFields" => [
                array(
                    'Key' => 'MailOptin',
                    'Value' => true,
                )
            ],
            "Resubscribe" => true,
            "RestartSubscriptionBasedAutoresponders" => true
        ];

        $response = $this->addSubscriber($list_id, $payload);

        return 201 === $this->httpClient->getResponseHttpCode();
    }
}
