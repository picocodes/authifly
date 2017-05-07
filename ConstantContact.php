<?php
/*!
* Authifly
* https://hybridauth.github.io | https://github.com/hybridauth/hybridauth
*  (c) 2017 Authifly authors | https://hybridauth.github.io/license.html
*/

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\Exception;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Exception\UnexpectedApiResponseException;
use Authifly\Data;

/**
 * ConstantContact OAuth2 provider adapter.
 */
class ConstantContact extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.constantcontact.com/v2/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://oauth2.constantcontact.com/oauth2/oauth/siteowner/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://oauth2.constantcontact.com/oauth2/oauth/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://developer.constantcontact.com/docs/authentication/oauth-2.0-server-flow.html';

    protected $validateApiResponseHttpCode = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->apiRequestHeaders = [
            'Authorization' => 'Bearer ' . $this->config->get('access_token')
        ];
    }

    /**
     * Return API key supplied as AuthiFly config.
     *
     * @return mixed
     */
    protected function apiKey()
    {
        return $this->config->filter('keys')->get('key');
    }

    /**
     * @param array $headers you could use this to supply your own authorization header with access token.
     *
     * E.g $constantcontact->getContactList(['Authorization' => 'Bearer ' . 'a8a8f842-a420-4d72-6ye7-25323f4e4934'])
     *
     * @return object
     */
    public function getContactList($headers = [])
    {
        return $this->apiRequest('lists', 'GET', ['api_key' => $this->apiKey()], $headers);
    }

    public function fetchContact($email_address, $headers = [])
    {
        return $this->apiRequest('contacts', 'GET', ['api_key' => $this->apiKey(), 'email' => $email_address], $headers);
    }

    public function createContact($email_address, $list_id, $first_name = '', $last_name = '', $headers = [])
    {
        $data = array();
        $data['email_addresses'] = array();
        $data['email_addresses'][0]['id'] = $list_id;
        $data['email_addresses'][0]['status'] = 'ACTIVE';
        $data['email_addresses'][0]['confirm_status'] = 'CONFIRMED';
        $data['email_addresses'][0]['email_address'] = $email_address;
        $data['lists'] = array();
        $data['lists'][0]['id'] = $list_id;
        if (!empty($first_name)) {
            $data['first_name'] = $first_name;
        }
        if (!empty($last_name)) {
            $data['last_name'] = $last_name;
        }

        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        return $this->apiRequest(sprintf('contacts?action_by=%s&api_key=%s', 'ACTION_BY_VISITOR', $this->apiKey()), 'POST', $data, $headers);
    }

    public function addContactToList($email_address, $list_id, $first_name = '', $last_name = '', $headers = [])
    {
        // Check if email already exists in Constant Contact.
        $contact = $this->fetchContact($email_address, $headers);

        // Bail if there was a problem.
        if (isset($contact->error_key)) {
            throw new UnexpectedApiResponseException($contact->error_key, $this->httpClient->getResponseHttpCode());
        }

        // If we have a previous contact, only update the list association.
        if (!empty($contact->results)) {
            $data = $contact->results[0];

            // Check if they are assigned to lists already.
            if (!empty($data->lists)) {
                foreach ($data->lists as $i => $list) {
                    // bail if they are already assigned.
                    if (isset($list->id) && $list_id == $list->id) {
                        return true;
                    }
                }

                // Otherwise, add them to the list.
                $new_list = new \stdClass;
                $new_list->id = $list_id;
                $new_list->status = 'ACTIVE';
                $data->lists[count($data->lists)] = $new_list;
            } else {
                // Add the contact to the list.
                $data->lists = array();
                $new_list = new \stdClass;
                $new_list->id = $list_id;
                $new_list->status = 'ACTIVE';
                $data->lists[0] = $new_list;
            }

            $contact_id = $contact->results[0]->id;

            $headers = array_replace(['Content-Type' => 'application/json'], $headers);

            $response = $this->apiRequest(sprintf('contacts/%d?api_key=%s&action_by=%s', $contact_id, $this->apiKey(), 'ACTION_BY_VISITOR'), 'PUT', $data, $headers);

            if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
                throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
            }

            return true;

        } else {

            $response = $this->createContact($email_address, $list_id, $first_name, $last_name, $headers);
            if (is_array($response) && isset($response[0]) && isset($response[0]->error_key)) {
                throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
            }

            return true;

        }
    }
}
