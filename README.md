# sendinblue/api-bundle

This bundle integrates [`sendinblue/api-v3-sdk`](https://github.com/sendinblue/APIv3-php-library) into Symfony.

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require sendinblue/api-bundle "~1"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new SendinBlue\Bundle\ApiBundle\SendinBlueApiBundle(),
        );

        // ...
    }

    // ...
}
```

## Configuration

```yaml
sendinblue_api:
    endpoints: []
    key: ~
```

You’ll define the API client key under `sendinblue_api.key`.

Then you’ll need to otp-in for the endpoints you want to access. One service will be created per endpoint:

- account
- attributes
- contacts
- email_campaigns
- folders
- lists
- process
- reseller
- senders
- sms_campaigns
- smtp
- transactional_sms
- webhooks

The service names will be `sendinblue_api.%s_endpoint` where `%s` is a value from the list above. The corresponding classes can be found under the `SendinBlue\Client\Api` namespace.

```php
/** @var SendinBlue\Client\Api\AccountApi $accountEndpoint */
$accountEndpoint = $this->get('sendinblue_api.account_endpoint');
```

### Multiple clients

To use more than one client you must move `key` and `endpoints` parameter under a `clients` associative array. Each client will be named by its key.

```yaml
sendinblue_api:
    clients:
        first:
            endpoints:
                - account
            key: ~
        second:
            endpoints:
                - account
            key: ~
```

To access the first client account endpoint you’ll get the `sendinblue_api.first_client.account_endpoint` service.

#### Default client

You can define a default client through the `default_client` parameter; by default it is the first defined client. You can access the default client endpoints without adding its name in the service name. In the above example the `sendinblue_api.account_endpoint` service would be an alias of `sendinblue_api.first_client.account_endpoint`.

If you write

```yaml
sendinblue_api:
    default_client: second
    clients:
        first:
            endpoints:
                - account
            key: ~
        second:
            endpoints:
                - account
            key: ~
```

then `sendinblue_api.account_endpoint` will be an alias of `sendinblue_api.second_client.account_endpoint`.