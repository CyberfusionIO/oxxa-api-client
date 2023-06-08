# Oxxa API client

PHP client for the [Oxxa API](https://www.oxxa.com/domeinnamen/api).

## Support

This client was written by Cyberfusion. It has been open sourced for the community. If you have any questions, open an 
issue on GitHub or email support@cyberfusion.nl.

This client is not supported by Oxxa.

## Requirements

This client requires PHP 8.1 or higher with default PHP modules.

## Installation

This client can be used in any PHP project and with any framework.

Install the client with Composer:

```bash
composer require cyberfusion/oxxa-api-client
```

## Usage

### Getting started

```php
// Initialize the API
$oxxa = new Oxxa($username, $password);

// Perform calls to an endpoint
$available = $oxxa
    ->domain()
    ->check('cyberfusion.nl');
```

### Test mode

To test your implementation, use the test mode. All requests tell the Oxxa API that the request is a test.

Enable: 

```php
$oxxa->enableTestMode();
```

Disable:

```php
$oxxa->disableTestMode();
```

### Exceptions

In case of errors, the client throws the exception `OxxaException`. 

All exceptions have a code. These can be found in the `OxxaException` class.

## Tests

Unit tests are available in the `tests` directory. Run:

`composer test`

To generate a code coverage report in the `build/report` directory, run:

`composer test-coverage`

## Contribution

Some basic endpoints are implemented, but there are a lot which are not implemented yet. Feel free to contribute! See 
the [contributing guidelines](CONTRIBUTING.md).

## Security

If you discover any security related issues, please email support@cyberfusion.nl instead of using the issue tracker.

## License

This client is open-sourced software licensed under the [MIT license](http://support.org/licenses/MIT).
