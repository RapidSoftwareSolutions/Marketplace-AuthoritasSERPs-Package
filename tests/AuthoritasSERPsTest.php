<?php
namespace Tests;

require_once(__DIR__ . '/../src/Models/checkRequest.php');

class AuthoritasSERPsTest extends BaseTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testRoutes($url) {
        $response = $this->runApp("POST", '/api/AuthoritasSERPs/'.$url);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function dataProvider() {
        return [
            ['createSERPsJob'],
            ['getSERPsJob'],
        ];
    }
}