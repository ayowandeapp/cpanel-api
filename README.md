# cPanel API Client

A simple and lightweight PHP client for interacting with the cPanel API using cURL. This package allows you to easily manage cPanel accounts, domains, emails, and other server resources through a straightforward API.

## Features

- Simple and intuitive API for interacting with cPanel
- Supports GET and POST requests to cPanel's API endpoints
- Easy configuration via constructor parameters
- Handles authentication with cPanel's API tokens

## Installation

Install the package via Composer:

```bash
composer require ayowande/cpanel-api
```

## Usage

**Initialization**

First, create an instance of the Cpanel class with your cPanel server details:

```php
use ayowande\Cpanel\Cpanel;

$cpanel = new Cpanel(
    'example.com',    // cPanel server hostname
    'username',       // cPanel username
    2083,             // Port (usually 2083 for SSL)
    'your-api-token'  // cPanel API token
);
```

**Sending a GET Request**

To send a GET request to a specific cPanel API endpoint:

```php
$response = $cpanel->get('/json-api/listaccts', [
    'api.version' => 1
]);

echo $response;
```

**Sending a POST Request**

To send a POST request with data:

```php
$response = $cpanel->post('/json-api/createacct', [
    'api.version' => 1,
    'username' => 'newuser',
    'domain' => 'newdomain.com',
    'plan' => 'default'
]);

echo $response;
```

**check out the test for more examples on various use cases**

- [Test](./tests/CpanelTest.php)

```
vendor/bin/phpunit
```
**Handling Errors**

Both get and post methods throw exceptions if the cURL request fails. You can handle these exceptions like this:

```php
try {
    $response = $cpanel->get('/json-api/listaccts', [
        'api.version' => 1
    ]);

    echo $response;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Requirements

- PHP 8.0 or higher
- cURL extension enabled
- cPanel API token

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss any changes.
