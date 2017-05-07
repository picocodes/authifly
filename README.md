## Authifly

### Constant Contact Usage

```
<?php

require './vendor/autoload.php';

$config = [
    'callback' => 'http://mailoptin.app/test.php',
    'keys' => ['key' => '57kxrwcayg4ntpb2vm3ywyuo', 'secret' => '2hSC4tbfB2xSwyY8MkP4D3vx'],
    'access_token' => '9oa8f842-a420-hg72-9ee7-45328f4e4937'
];

try {
    $constantcontact = new \Authifly\Provider\ConstantContact($config);

//    $constantcontact->authenticate();


//    var_dump($constantcontact->getAccessToken());
//    var_dump($constantcontact->fetchContact('collizo4sky@gmail.com'));
    var_dump($constantcontact->getContactList());
    var_dump($constantcontact->addContactToList('puff66@mailoptin.app', '1625760192', 'Puff', 'Puff2'));

//    var_dump($constantcontact->getContactList());

//    $a = $constantcontact->httpClient->getResponseHttpCode();
//    $b = $constantcontact->httpClient->getResponseBody();
//
//    var_dump($a, $b);

} catch (\Exception $e) {

    $a = $constantcontact->httpClient->getResponseHttpCode();
    $b = $constantcontact->httpClient->getResponseBody();
    $b = $constantcontact->httpClient->getResponseClientError();

    var_dump($a);
    var_dump($b);

    echo 'Oops, we ran into an issue! ' . $e->getMessage();
}
```