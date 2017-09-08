<?php

namespace SendinBlue\Bundle\ApiBundle\Tests\Functional;

use SendinBlue\Client\Api\AccountApi;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BundleTest extends KernelTestCase
{
    public function testDefaultClientEndpointAsService()
    {
        static::bootKernel([
            'config' => 'config.xml',
        ]);
        $container = static::$kernel->getContainer();

        /** @var AccountApi $endpoint */
        $endpoint = $container->get('sendinblue_api.account_endpoint');

        $this->assertInstanceOf('SendinBlue\Client\Api\AccountApi', $endpoint);
        $this->assertSame($endpoint, $container->get('sendinblue_api.default_client.account_endpoint'));

        $this->assertSame($endpoint->getApiClient()->getConfig()->getApiKey('api-key'), 'key');
    }

    public function testSkippedConfigurationClientsNode()
    {
        static::bootKernel([
            'config' => 'config.php',
        ]);
        $container = static::$kernel->getContainer();

        /** @var AccountApi $endpoint */
        $endpoint = $container->get('sendinblue_api.account_endpoint');

        $this->assertInstanceOf('SendinBlue\Client\Api\AccountApi', $endpoint);
        $this->assertSame($endpoint, $container->get('sendinblue_api.defined_client.account_endpoint'));

        $this->assertSame($endpoint->getApiClient()->getConfig()->getApiKey('api-key'), 'key');
    }

    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass()
    {
        return 'SendinBlue\Bundle\ApiBundle\Tests\Functional\app\AppKernel';
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        return new static::$class($options['config']);
    }
}
