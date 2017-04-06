<?php

$app->post('/api/AuthoritasSERPs/createSERPsJob', function ($request, $response) {
    /** @var \Slim\Http\Response $response */
    /** @var \Slim\Http\Request $request */
    /** @var \Models\checkRequest $checkRequest */

    $settings = $this->settings;
    $checkRequest = $this->validation;
    $validateRes = $checkRequest->validate($request, ['apiKey', 'apiSalt', 'apiSecret']);
    if (!empty($validateRes) && isset($validateRes['callback']) && $validateRes['callback'] == 'error') {
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($validateRes);
    } else {
        $postData = $validateRes;
    }

    $url = $settings['apiUrl'] . "/serps/";

    $time = time();
    $hashSource = $time . $postData['args']['apiKey'] . $postData['args']['apiSalt'];
    $hash = hash_hmac('sha256', $hashSource, $postData['args']['apiSecret']);
    $test = 'KeyAuth';
    $test .= ' publicKey=' . $postData['args']['apiKey'];
    $test .= ' hash=' . $hash;
    $test .= ' ts=' . $time;

    $json = [
        "search_engine" => "google",
        "region" => "global",
        "language" => "en",
        "max_results" => 10,
        "phrase" => "How good is your SEO"
    ];

    try {
        /** @var GuzzleHttp\Client $client */
        $client = $this->httpClient;
        $vendorResponse = $client->post($url, [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => $test
            ],
            'json' => $json
        ]);
        $vendorResponseBody = $vendorResponse->getBody()->getContents();
        if ($vendorResponse->getStatusCode() == 201) {
            $result['callback'] = 'success';
            $result['contextWrites']['to'] = json_decode($vendorResponse->getBody());
        } else {
            $result['callback'] = 'error';
            $result['contextWrites']['to']['status_code'] = 'API_ERROR';
            $result['contextWrites']['to']['status_msg'] = is_array($vendorResponseBody) ? $vendorResponseBody : json_decode($vendorResponseBody);
        }
    } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
        $vendorResponseBody = $exception->getResponse()->getBody();
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'API_ERROR';
        $result['contextWrites']['to']['status_msg'] = json_decode($vendorResponseBody);
    }

    return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
});
