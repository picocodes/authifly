<?php

require './vendor/autoload.php';

$config = [
    'callback' => 'http://mailoptin.app/test.php',
    'keys' => ['key' => '89kxrwcayg4ntpb2vm3ytryu', 'secret' => '5gSC4tbfB2xSwyY8MkP4D6yu'],
    'access_token' => 't6a8f842-a420-4c72-9ee7-35328f4e4678'
];

try {
    $constantcontact = new \Authifly\Provider\ConstantContact($config);

//    $constantcontact->authenticate();


//    var_dump($constantcontact->getAccessToken());
//    var_dump($constantcontact->fetchContact('4sky@gmail.com'));
//    var_dump($constantcontact->getContactList()); exit;
//    var_dump($constantcontact->addContactToList('puff66@sitepoint.com', '1925760193', 'Puff', 'Puff2'));

    $email_content = <<<HTML
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Hello world! W3Guy</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<style type="text/css">@media only screen and (max-width: 600px) {
      .email-body_inner,
      .email-footer {
        width: 100% !important;
      }
    }@media only screen and (max-width: 500px) {
      .button {
        width: 100% !important;
      }
    }</style>
</head>
<body style="font-family: Arial, &quot;Helvetica Neue&quot;, Helvetica, sans-serif; box-sizing: border-box; height: 100%; margin: 0px; line-height: 1.4; color: rgb(116, 120, 126); text-size-adjust: none; width: 100% !important; overflow-x: hidden;">
          <table class="email-wrapper mo-page-bg-color" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0; background-color: #eeee22;"><tbody><tr>
<td align="center">
        <table class="email-content" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0;">
<!-- Logo --><tbody><tr>
<td class="email-masthead" style="padding: 25px 0; text-align: center;">
            <a class="webversion-label mo-header-web-version-label mo-header-web-version-color" href="{{webversion}}" style="color: #74787e; font-size: 10px;">View this email in your browser</a><br><br><div class="email-masthead_name mo-header-text mo-header-text-color" style="font-size: 25px; font-weight: bold; text-decoration: none; color: #bbbfc3;">WordPresss</div>
            </td>
          </tr>
<!-- Email Body --><tr>
<td class="email-body mo-content-background-color mo-content-body-font-size mo-content-alignment" width="100%" style="width: 100%; margin: 0; padding: 0; text-align: center; background-color: #ffffff; font-size: 16px;">
              <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" style="width: 570px; margin: 0 auto; padding: 0;">
<!-- Body content --><tbody><tr>
<td class="content-cell mo-content-text-color" style="padding: 35px; color: #74787e;">
                    <a href="http://wordpress.dev/hello-world/" style="color: #3869D4; text-decoration: none;">
                    <h1 class="mo-content-title-font-size" style="margin-top: 0; color: #2F3133; font-weight: bold; font-size: 19;">Hello world! W3Guy</h1>
                    <img class="" src="http://wordpress.dev/wp-content/plugins/mailoptin/Core/assets/images/email-templates/default-feature-img.jpg" style="max-width: 500px; padding-bottom: 10px;"></a>
                    <p style="margin-top: 0; line-height: 1.5em;">Welcome to WordPress. This is your first post. Edit or delete it, then start writing!<br>
Welcome to WordPress. This is your first post. Edit or delete it, then start writing!<br>
Welcome to WordPress. This is your first post. Edit or delete it, then start writing!<br>
                    <table class="body-action" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 30px auto; padding: 0;"><tbody><tr>
<td><div class="mo-content-button-alignment" style="text-align: center; float: center;"><a href="http://wordpress.dev/hello-world/" class="button button--red mo-content-button-background-color mo-content-button-text-color mo-content-read-more-label" style="color: #ffffff; display: inline-block; width: 200px; border-radius: 3px; font-size: 15px; line-height: 45px; text-align: center; text-decoration: none; -webkit-text-size-adjust: none; mso-hide: all; background-color: #dc4d2f;">Read more</a>
                          </div> </td></tr></tbody></table>
</td></tr>
</tbody></table>
</td> </tr>
<tr>
<td>
              <table class="email-footer mo-footer-text-color mo-footer-font-size" align="center" width="570" cellpadding="0" cellspacing="0" style="width: 570px; margin: 0 auto; padding: 0; text-align: center; color: #aeaeae; font-size: 12px;"><tbody><tr>
<td class="content-cell" style="padding: 35px;">
                    <p class="sub center mo-footer-copyright-line" style="margin-top: 0; line-height: 1.5em; text-align: center;">© 2016 WordPresss. All rights reserved.</p>
                    <p class="sub center mo-footer-description" style="margin-top: 0; line-height: 1.5em; text-align: center;">Our mailing address is:<br>
WordPresss<br>
{{company_address_line1}}<br>
{{company_address_line2}}</p>
                    <p class="sub center" style="margin-top: 0; line-height: 1.5em; text-align: center;"><span class="unsubscribe-line mo-footer-unsubscribe-line">If you do not want to receive emails from us, you can</span>  <a class="unsubscribe mo-footer-unsubscribe-link-label mo-footer-unsubscribe-link-color" href="{{unsubscribe}}" style="color: #74787e;">unsubscribe</a>.</p>
                  </td>
                </tr></tbody></table>
</td> </tr>
</tbody></table>
</td></tr></tbody></table>
</body></html>
HTML;
    $creat = $constantcontact->createEmailCampaign(
        array(
            'status' => 'SENT',
            'name' => 'Test Campaign html emai form API6' . mt_rand(),
            'subject' => 'Subject Test',
            'from_name' => 'W3Guy LLC',
            'from_email' => 'me@mailoptin.app',
            'reply_to_email' => 'me@mailoptin.app',
            'is_view_as_webpage_enabled' => true,
            'view_as_web_page_text' => 'View this message as a web page',
            'view_as_web_page_link_text' => 'Click here',
            'email_content' => $email_content,
            'text_content' => 'This is the text-only content of the email message for mail clients that do not support HTML.',
            'email_content_format' => 'HTML',
            'style_sheet' => '',
            'message_footer' =>
                array(
                    'organization_name' => 'W3Guy LLC',
                    'address_line_1' => '123 Maple Street',
                    'city' => 'Benin City',
                    'state' => 'Edo',
                    'international_state' => 'Edo State',
                    'country' => 'GH',
                ),
            'sent_to_contact_lists' => [
                [
                    "id" => "1729748067"
                ]
            ]
        )
    );

//    var_dump($constantcontact->getContactList());

//    $a = $constantcontact->httpClient->getResponseHttpCode();
//    $b = $constantcontact->httpClient->getResponseBody();
//
//    var_dump($a, $b);

    var_dump($constantcontact->scheduleEmailCampaign($creat->id, strtotime('+2 hours')));

} catch (\Exception $e) {

    $a = $constantcontact->httpClient->getResponseHttpCode();
    $b = $constantcontact->httpClient->getResponseBody();
    $b = $constantcontact->httpClient->getResponseClientError();

    var_dump($a);
    var_dump($b);

    echo 'Oops, we ran into an issue! ' . $e->getMessage();
}