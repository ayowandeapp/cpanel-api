<?php

namespace ayowande\Cpanel;

/**
 * Class Cpanal
 * handles connections to a remote cpanel server using cURL
 */

class Cpanel
{
    private String $host;
    private String $username;
    private String|int $port;
    private String $token;

    /**
     * Cpanel constructor
     * 
     * @param string $host The cPanel server hostname.
     * @param string $username The cPanel username.
     * @param string|int $port The port to connect to.
     * @param string $token The API token for authentication.
     */
    public function __construct(String $host, String $username, String|int $port, String $token)
    {
        $this->host = $host;
        $this->username = $username;
        $this->port = $port;
        $this->token = $token;
    }

    /**
     * Build the HTTP header required for the cURL
     * 
     * @return array The array of the HTTP header
     */
    private function curlHttpHeader(): array
    {
        //authentication using the username and token
        $httpHeaders = [
            "Authorization: cpanel {$this->username}:{$this->token}",
            "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0"
        ];
        return $httpHeaders;
    }

    /**
     * Sends a GET request to the specified endpoint.
     *
     * @param string $endpoint The API endpoint to send the request to.
     * @param array $data The query parameters to include in the request.
     * @return string The response from the server.
     * @throws \Exception If the cURL request fails.
     */
    public function get(String $endpoint, $data = []): string
    {
        $url = $this->buildUrl($endpoint, $data);
        return $this->executeCurlRequest($url, "GET");
    }

    /**
     * Sends a POST request to the specified endpoint.
     *
     * @param string $endpoint The API endpoint to send the request to.
     * @param array $data The data to include in the POST request.
     * @return string The response from the server.
     * @throws \Exception If the cURL request fails.
     */
    public function post(string $endpoint, array $data): string
    {
        $url = $this->buildUrl($endpoint);
        return $this->executeCurlRequest($url, "POST", $data);
    }

    /**
     * Builds the full URL for the cURL request.
     *
     * @param string $endpoint The API endpoint.
     * @param array $data The query parameters (optional).
     * @return string The full URL.
     */
    private function buildUrl(string $endpoint, array $data = []): string
    {
        $query = http_build_query($data);
        return "https://{$this->host}:{$this->port}{$endpoint}" . ($query ? "?{$query}" : '');
    }

    /**
     * Executes the cURL request.
     *
     * @param string $url The URL to send the request to.
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param array|null $postData The data for POST requests (optional).
     * @return string The response from the server.
     * @throws \Exception If the cURL request fails.
     */
    private function executeCurlRequest(string $url, string $method, array $postData = null): string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->curlHttpHeader());

        if ($method === "POST" && $postData) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception("cURL error: " . curl_error($curl) . " for URL: $url");
        }

        $this->disconnect($curl);
        return $response;
    }
    /**
     * disconnect from renote server
     * 
     * @param CurlHandle instance of the cURL
     */
    private function disconnect($curl)
    {
        // Clean up
        curl_close($curl);
    }
}
