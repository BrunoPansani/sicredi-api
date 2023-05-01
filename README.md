# Sicredi API

![Tests, PHP, PHP CS Fixer](https://github.com/BrunoPansani/sicredi-api/actions/workflows/ci.yml/badge.svg)

This package provides a PHP client for interacting with the Sicredi API. It allows developers to easily integrate Sicredi into their PHP applications. The client supports a wide range of features inside the Charges API, including creating and querying boletos, generating boletos slips, and retrieving payment reports.

The package is built on top of the Guzzle HTTP client and provides a simple, object-oriented API for interacting with the Sicredi API. It also includes comprehensive documentation and examples to help developers get started quickly.

While the package currently only supports the API for Boletos, it is designed with extensibility in mind, and can easily be extended to support additional functionality as needed.


## Installation

First, you will need to install [Composer](http://getcomposer.org/) following the instructions on their site.

Then, simply run the following command:

```sh
composer require BrunoPansani/sicredi-api
```

## Usage

Once you have installed it, you can use the following steps to create a new Boleto:

1. Create a new Client instance: Create a new Client instance by passing your SICREDI API key, cooperative number, post number, and beneficiary number as arguments. For example:

```php
$client = new \SicrediAPI\Client(
    $_ENV['SICREDI_API_KEY'],
    $_ENV['SICREDI_COOPERATIVE'],
    $_ENV['SICREDI_POST'],
    $_ENV['SICREDI_BENEFICIARY'],
    new \GuzzleHttp\Client(), true);
```
This example uses environment variables, but feel free to use any method you prefer to pass the parameters.

2. Call the authenticate() method on the Client instance to authenticate with the Sicredi API by passing your Sicredi API username and password as arguments.

3. Get the Boleto resource client from the Client instance by calling the boleto() method. For example:

```php
$client->authenticate($_ENV['SICREDI_USERNAME'], $_ENV['SICREDI_PASSWORD']);

$boletoClient = $client->boleto();
```

4. Create a new Boleto instance by passing the necessary parameters, such as beneficiary information, payee information, amount, and due date. For example:

```php
$boleto = new \SicrediAPI\Domain\Boleto\Boleto(
    (new Beneficiary(
        'Jose da Silva',
        '86049253099',
        'person'
    )),
    (new Payee(
        'Maria de Lurdes',
        '50581718054',
        'person'
    )),
    100.00,
    'DM',
    12345,
    'RECIBO',
    '999999',
    new DateTime('2023-12-31')
);
```

5. Call the create() method on the Boleto resource client to create the Boleto. For example:

```php
$boletoClient->create($boleto);
```

6. Store and/or do anything else you need to do with the Boleto information returned by the create() method.

## Validation and Production


Before using the Cobrança API friom Sicredi, please follow the sequential steps for validation and production contained in the attached manual, [available at docs/](https://github.com/BrunoPansani/sicredi-api/tree/main/docs).

In summary, the process for validation includes the following steps:

1. Access the Developer Portal
2. Create an APP for Sandbox
3. Request the Sandbox API Token
4. Test the URLs available in the Homologation environment
5. Create an APP for Production
6. Request the Production API Token
7. Test the URLs available in the Production environment
8. Deploy your solution

To create the Sandbox APP, the developer must access the [Sicredi Developer Portal](https://developer.sicredi.com.br/), create an account or log in, and create a new application with the prefix "API Cobrança <Cooperative> <Beneficiary Code> Sandbox".

After creating the Sandbox APP, the developer should request the Sandbox API Token through the menu 'Suporte' > 'Abrir Chamado' in the Developer Portal. Select the appropriate option and fill in the name of the APP created previously. The API Token will be generated within a few days and can be found in the 'Minhas Apps' menu, under the details of the respective application.

The URLs available for testing can be found in the API manual.

For the production process, repeat the same steps for creating the APP, but with a different name, such as "API Cobrança <Cooperative> <Beneficiary Code> Production". After creating the APP, request the API Token for Production, following the same steps as for Sandbox.

Remember that, in all operations, the authentication token received must be informed in the `x-api-key` header of the request. This token is different for each environment.

If you have any doubts, please contact the support channels listed in the attached manual, or visit the [Developer Portal](https://developer.sicredi.com.br/).

## Contributing

We welcome contributions to this package! If you would like to contribute, please follow these guidelines:

1. Fork the repository and make your changes.
2. Submit a pull request with a clear explanation of your changes and why they are necessary.
3. Be responsive to feedback and open to making changes to your pull request.

Remember you can always check Sicredi's documentation [available at docs/](https://github.com/BrunoPansani/sicredi-api/tree/main/docs).

Thank you for your interest in contributing to this package!

## License
This package is released under the [MIT License](https://github.com/BrunoPansani/sicredi-api/tree/main/LICENSE).

## Contact
If you have any questions or issues, please contact me at bruno@pansani.dev.