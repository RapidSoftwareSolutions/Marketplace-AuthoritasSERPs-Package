<?php

$app->post('/api/AuthoritasSERPs/createSERPsJob', function ($request, $response) {
    /** @var \Slim\Http\Response $response */
    /** @var \Slim\Http\Request $request */
    /** @var \Models\checkRequest $checkRequest */

    $settings = $this->settings;
    $checkRequest = $this->validation;
    $validateRes = $checkRequest->validate($request, ['apiKey', 'apiSalt', 'apiSecret', 'searchEngine', 'phrase', 'region']);
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

    $json['search_engine'] = $postData['args']['searchEngine'];
    $json['phrase'] = $postData['args']['phrase'];
    $json['region'] = $postData['args']['region'];

    if (!empty($postData['args']['language'])) {
        $json['language'] = $postData['args']['language'];
    }
    if (!empty($postData['args']['town'])) {
        $json['town'] = $postData['args']['town'];
    }
    if (!empty($postData['args']['searchType'])) {
        $json['search_type'] = $postData['args']['searchType'];
    }
    if (!empty($postData['args']['maxResults'])) {
        $json['max_results'] = (int) $postData['args']['maxResults'];
    }
    if (!empty($postData['args']['strategy'])) {
        $json['strategy'] = $postData['args']['strategy'];
    }
    if (!empty($postData['args']['fullPagesCount'])) {
        $json['full_pages_count'] = (int) $postData['args']['fullPagesCount'];
    }
    if (!empty($postData['args']['userAgent'])) {
        $json['user_agent'] = $postData['args']['userAgent'];
    }
    if (isset($postData['args']['useCache']) && strlen($postData['args']['useCache']) > 0) {
        $json['use_cache'] =  filter_var($postData['args']['useCache'], FILTER_VALIDATE_BOOLEAN);
    }
    if (isset($postData['args']['includeAllInUniversal']) && strlen($postData['args']['includeAllInUniversal']) > 0) {
        $json['include_all_in_universal'] = $postData['args']['includeAllInUniversal'];
    }

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
