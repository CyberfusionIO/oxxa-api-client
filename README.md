# Oxxa API client

PHP client for the [Oxxa API](https://www.oxxa.com/domeinnamen/api).

## Support

This client is NOT officially supported by Oxxa. If you have any questions, open an issue at this repository.

This client is created by Cyberfusion, a hosting provider with the best hosting platform for agencies.

## Requirements

This client requires PHP 8.1 or higher and requires several php modules. These modules are installed by default, so 
probably won't be any issue.

## Installation

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

To test your implementation, enable the test mode. All request will tell the Oxxa API that the request is a test:

```php
$oxxa->enableTestMode();
```

Or disable the test mode:

```php
$oxxa->disableTestMode();
```

### Exceptions

In case of errors, the client throws an exception `OxxaException`. 

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
