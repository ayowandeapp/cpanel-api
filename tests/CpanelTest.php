<?php

use ayowande\Cpanel\Cpanel;
use PHPUnit\Framework\TestCase;

class CpanelTest extends TestCase
{

    private $cpanel;

    protected function setUp(): void
    {
        $this->cpanel = new Cpanel(
            'jsonplaceholder.typicode.com',
            'test_user',
            443,
            'test_token'
        );
    }

    public function testGetRequest()
    {
        $response = $this->cpanel->get('/todos/1');

        $response = json_decode($response, true);

        $this->assertEquals(1, $response['id']);
    }

    public function testPostRequest()
    {
        $expectedResponse = [
            'title' => 'foo',
            'body' => 'bar',
            'userId' => '1',
        ];
        $response = $this->cpanel->post('/posts', $expectedResponse);
        $response = json_decode($response, true);
        // Example assertion
        $this->assertEquals(101, $response['id']);
    }


    /**
     * The methods below are examples of enpoints to use 
     * to make certain changes in the cpanel
     */

    /** 
     * Creating a database in cPanel.
     */
    private function testCreateDatabase()
    {
        //instantiate the cpanel with your details
        $cpanel = $this->cpanel;
        $endpoint = "/execute/Mysql/create_database";
        $database_name = 'db-1';
        $data = [
            'name' => $database_name,
        ];
        $response = $cpanel->get($endpoint, $data);
        $this->assertStringContainsString('success', $response);
    }

    /** 
     * Test for creating a database user in cPanel.
     */
    private function testCreateUser()
    {
        //instantiate the cpanel with your details
        $cpanel = $this->cpanel;
        $endpoint = "/execute/Mysql/create_user";
        $database_name = 'db-1';
        // Generate 8 digit random number with string
        $randomNumber = rand(1000, 9999);
        $randomString = substr(md5(rand()), 0, 8);
        $pass = "{$randomNumber}-{$randomString}";
        $data = [
            'name' => $database_name,
            'password' => $pass,
        ];
        // Execute cURL 
        $response = $cpanel->get($endpoint, $data);
        $this->assertStringContainsString('success', $response);
    }

    /**
     * Test setting privileges on a database in cPanel.
     */
    private function testSetPrivileges(): void
    {
        //instantiate the cpanel with your details
        $cpanel = $this->cpanel;
        $endpoint = "/execute/Mysql/set_privileges_on_database";
        $database_name = 'db-1';
        $data = [
            'user' => $database_name,
            'database' => $database_name,
            'privileges' => 'ALL PRIVILEGES',
        ];
        // Execute cURL 
        $response = $cpanel->get($endpoint, $data);
        $this->assertStringContainsString('success', $response);
    }

    /**
     * Test for creating a subdomain in cPanel.
     */
    private function testCreateSubdomain()
    {
        //instantiate the cpanel with your details
        $cpanel = $this->cpanel;
        $endpoint = "/execute/SubDomain/addsubdomain";
        $subdomain = "test";
        $hostname = "http://localhost.com";
        $path = "/public_html/directory_name";
        $data = [
            'domain' => $subdomain,
            'rootdomain' => $hostname,
            'dir' => '/' . $path, // relative to the user's home directory /public_html/directory_name
            'disallowdot' => '1',
        ];
        // Execute cURL 
        $response = $cpanel->get($endpoint, $data);
        $this->assertStringContainsString('success', $response);
    }
    /**
     * Test for creating a file on the server via cPanel.
     */
    private function testCreateFile()
    {
        //instantiate the cpanel with your details
        $cpanel = $this->cpanel; //destination path   
        $destination_dir = "/public_html/directory_name";
        $app_url = "https://name.hostnme.com";
        //set environment variables
        $content_array = [
            'APP_URL' => $app_url,
            'JWT_TTL' => 1440,
            'LOG_CHANNEL' => 'stack',
            'DB_CONNECTION' => 'mysql',
            'MAIL_PASSWORD' => 'rrtrgrgrrrrvvr',
            'MAIL_ENCRYPTION' => 'null',
            'MAIL_FROM_ADDRESS' => 'null',
            'BROADCAST_DRIVER' => 'log',
            'CACHE_DRIVER' => 'file',
            'QUEUE_CONNECTION' => 'database',
            'SESSION_DRIVER' => 'file',
            'SESSION_LIFETIME' => 120,
            'REDIS_HOST' => '127.0.0.1',
            'REDIS_PASSWORD' => 'null',
            'REDIS_PORT' => 6379,
            'PUSHER_APP_ID' => '',
            'PUSHER_APP_KEY' => '',
            'PUSHER_APP_SECRET' => '',

        ];

        $data = '';
        foreach ($content_array as $key => $value) {
            $data .= "\n{$key}={$value}";
        }
        // create .env file on remote server
        $endpoint = '/execute/Fileman/save_file_content';
        $data = [
            'dir' => $destination_dir,
            'file' => '.env',
            'from_charset' => 'UTF-8',
            'to_charset' => 'ASCII',
            'content' => $data,
        ];
        // Execute cURL 
        $response = $cpanel->get($endpoint, $data);
        $this->assertStringContainsString('success', $response);
    }
}
