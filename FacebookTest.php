<?php
require './vendor/autoload.php';

$config = [
    'callback' => Authifly\HttpClient\Util::getCurrentUrl(),
    'keys' => ['id' => '458788207890960', 'secret' => 'dbe2110f22fe70ef34a86230cf6f503f'],
    'scope' => 'ads_management'
//    'access_token' => 't6a8f842-a420-4c72-9ee7-35328f4e4678'
];

try {
    $fb = new \Authifly\Provider\Facebook($config);

    $fb->authenticate();

    $user_id = $fb->getUserProfileId();

    var_dump($user_id);

    $adAccounts = $fb->getAdAccountIds($user_id);

    var_dump($adAccounts);

    $permissions = $fb->fetchPermissions($user_id);
    $permissions1 = $fb->getPixelId('364830746');

    var_dump($permissions, $permissions1);


    var_dump($fb->getAccessToken());

    var_dump($fb->hasAccessTokenExpired());
    // if acces token is expired, create an admin notice with link to re-authorize.

    $custom_audience_id = $fb->createCustomAudience($adAccounts[0]->account_id, 'mailoptin custom aud', 'powered by mailoptin.io');

    var_dump($custom_audience_id);

    if ($custom_audience_id) {
        $resp = $fb->addUserToCustomAudience($custom_audience_id, 'collizo4sky@aol.com', 'Collins', 'Agbonghama');
        var_dump($resp);
    }

    $fb->disconnect();


} catch (\Exception $e) {

//    $fb->disconnect();
//    $fb->authenticate();

//    var_dump($a);
//    var_dump($b);

    // log the error message when an operation failed. viewable in the auth page in connections
    echo 'Oops, we ran into an issue! ' . $e->getMessage();
}