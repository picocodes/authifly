<?php
/*!
* Authifly
* https://Authifly.github.io | https://github.com/Authifly/Authifly
*  (c) 2017 Authifly authors | https://Authifly.github.io/license.html
*/
namespace Authifly\Provider;

use Authifly\Adapter\OAuth1;
use Authifly\Exception\Exception;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Exception\UnexpectedApiResponseException;
use Authifly\Data;

/**
 * Twitter provider adapter.
 *
 * Example:
 *
 *   $config = [
 *       'callback'  => Authifly\HttpClient\Util::getCurrentUrl(),
 *       'keys'      => [ 'key' => '', 'secret' => '' ], // OAuth1 uses 'key' not 'id'
 *       'authorize' => true
 *   ];
 *
 *   $adapter = new Authifly\Provider\Twitter( $config );
 *
 *   try {
 *       $adapter->authenticate();
 *
 *       $userProfile = $adapter->getUserProfile();
 *       $tokens = $adapter->getAccessToken();
 *       $contacts = $adapter->getUserContacts(['screen_name' =>'andypiper']); // get those of @andypiper
 *       $activity = $adapter->getUserActivity('me');
 *   }
 *   catch( Exception $e ){
 *       echo $e->getMessage() ;
 *   }
 */
class Aweber extends OAuth1
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.aweber.com/1.0/';
    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://auth.aweber.com/1.0/oauth/authorize';
    /**
     * {@inheritdoc}
     */
    protected $requestTokenUrl = 'https://auth.aweber.com/1.0/oauth/request_token';
    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://auth.aweber.com/1.0/oauth/access_token';
    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://labs.aweber.com/docs/authentication';

    /**
     * Fetch account details
     *
     * @return mixed
     */
    public function fetchAccount()
    {
        /**
         * object(stdClass)[12]
         * public 'total_size' => int 1
         * public 'start' => int 0
         * public 'entries' =>
         * array (size=1)
         * 0 =>
         * object(stdClass)[10]
         * public 'http_etag' => string '"b74b526122119aa35af824f91214febb63456678-ca5feee2b7fbb6febfca8af5541541ea960aaedb"' (length=83)
         * public 'lists_collection_link' => string 'https://api.aweber.com/1.0/accounts/1158045/lists' (length=49)
         * public 'self_link' => string 'https://api.aweber.com/1.0/accounts/1158045' (length=43)
         * public 'resource_type_link' => string 'https://api.aweber.com/1.0/#account' (length=35)
         * public 'id' => int 1158045
         * public 'integrations_collection_link' => string 'https://api.aweber.com/1.0/accounts/1158045/integrations' (length=56)
         * public 'resource_type_link' => string 'https://api.aweber.com/1.0/#accounts' (length=36)
         */
        $response = $this->apiRequest('accounts');

        $data = new Data\Collection($response);
        $entries = $data->filter('entries')->toArray();

        return $entries[0];
    }

    /**
     * Get account ID
     *
     * @return int
     */
    public function fetchAccountId()
    {
        return $this->fetchAccount()->id;
    }

    /**
     * Fetch email/subscriber list.
     *
     * @param int $account_id
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function fetchEmailList($account_id)
    {
        if (empty($account_id)) {
            throw new InvalidArgumentException('Account ID is missing');
        }

        $response = $this->apiRequest("accounts/$account_id/lists");

        $data = new Data\Collection($response);

        return $data->filter('entries')->toArray();
    }

    /**
     * Fetch email/subscriber list.
     *
     * @param int $account_id
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function fetchEmailListNameAndId($account_id)
    {
        /**
         * array (size=2)
         * 0 =>
         * array (size=2)
         * 0 => int 4687989
         * 1 => string 'Blog subscribers' (length=16)
         * 1 =>
         * array (size=2)
         * 0 => int 4688698
         * 1 => string 'Software buyers' (length=15)
         */
        if (empty($account_id)) {
            throw new InvalidArgumentException('Account ID is missing');
        }

        $response = $this->fetchEmailList($account_id);

        return array_reduce($response, function ($carry, $item) {
            $carry[] = [$item->id, $item->name];

            return $carry;
        });
    }

    public function addSubscriber($account_id, $list_id, $payload = [])
    {
        if (empty($account_id) || empty($list_id) || empty($payload)) {
            throw new InvalidArgumentException('Account ID or list ID or payload is missing');
        }

        $parameters = array_merge(['ws.op' => 'create'], $payload);

        return $this->apiRequest("accounts/$account_id/lists/$list_id/subscribers", 'POST', $parameters);

    }

    /**
     * Add subscriber supplying just email and name(optional)
     *
     * @param int $account_id
     * @param int $list_id
     * @param string $email
     * @param string $name
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function addSubscriberEmailAndName($account_id, $list_id, $email, $name = '')
    {
        if (empty($account_id) || empty($list_id) || empty($email)) {
            throw new InvalidArgumentException('Account ID or list ID or email address is missing');
        }

        try {
            $payload = ['email' => $email, 'name' => $name, 'ip_address' => $_SERVER['REMOTE_ADDR']];

            $this->addSubscriber($account_id, $list_id, $payload);

            return 201 === $this->httpClient->getResponseHttpCode();

        } catch (Exception $e) {

            $httpStatusCode = $this->httpClient->getResponseHttpCode();
            $httpResponseBody = $this->httpClient->getResponseBody();

            if (400 === $httpStatusCode && strpos($httpResponseBody, 'already subscribed')) {
                return true;
            }

            throw new $e;
        }
    }
}