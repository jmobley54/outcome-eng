<?php
namespace OutcomeEngenuity;

use Config;
use League\OAuth2\Client\Provider;
use GuzzleHttp\Client;

class API{

  public function __construct() {
    $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => \OE_API_KEY,// The client ID assigned to you by the provider
        'clientSecret'            => \OE_API_SECRET,   // The client password assigned to you by the provider
        'redirectUri'             => 'urn:ietf:wg:oauth:2.0:oob',
        'urlAuthorize'            => \OE_API_URL . '/oauth/authorize',
        'urlAccessToken'          => \OE_API_URL . '/oauth/token',
        'urlResourceOwnerDetails' => '',
    ]);
  }

  public function queryString($endpoint, $params) {
    try {
      $client = new Client([
        'headers' => [
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this->token->getToken(),
        ]]
      );
      $response = $client->request('GET',
        \OE_API_URL . '/api/v1' . $endpoint, [ 'query' => $params ]
      );

      $body = (string) $response->getBody();
      return json_decode($body);
    } catch (Exception $e) { 
      \wp_mail(\OE_ADMIN_EMAILS, 'Outcome Engenuity API Plugin Error', $e->getMessage());
    }
  }

  public function post($endpoint, $params) {
    $client = new Client([
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $this->token->getToken(),
      ]]
    );

    $response = $client->request('POST',
      \OE_API_URL . '/api/v1' . $endpoint, [ 'form_params' => $params ]
    );

    $body = (string) $response->getBody();
    return json_decode($body);
  }

  public function getAccessToken() {
    try {
      $this->token = $this->provider->getAccessToken('password', [
          'username' => \OE_API_USER,
          'password' => \OE_API_PASSWORD,
      ]);
    } catch (\TypeError $e) { 
      \wp_mail(\OE_ADMIN_EMAILS, 'Outcome Engenuity API Plugin Error', $e->getMessage());
    }
  }
}
