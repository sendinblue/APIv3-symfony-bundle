<?php

namespace SendinBlue\Bundle\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SendinBlueApiExtension extends Extension
{
    private static $MAPPING = [
        'account' => 'SendinBlue\Client\Api\AccountApi',
        'attributes' => 'SendinBlue\Client\Api\AttributesApi',
        'contacts' => 'SendinBlue\Client\Api\ContactsApi',
        'email_campaigns' => 'SendinBlue\Client\Api\EmailCampaignsApi',
        'folders' => 'SendinBlue\Client\Api\FoldersApi',
        'lists' => 'SendinBlue\Client\Api\ListsApi',
        'process' => 'SendinBlue\Client\Api\ProcessApi',
        'reseller' => 'SendinBlue\Client\Api\ResellerApi',
        'senders' => 'SendinBlue\Client\Api\SendersApi',
        'sms_campaigns' => 'SendinBlue\Client\Api\SMSCampaignsApi',
        'smtp' => 'SendinBlue\Client\Api\SMTPApi',
        'transactional_sms' => 'SendinBlue\Client\Api\TransactionalSMSApi',
        'webhooks' => 'SendinBlue\Client\Api\WebhooksApi',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration(), $config);

        if (empty($config['default_client'])) {
            $keys = \array_keys($config['clients']);
            $config['default_client'] = \reset($keys);
        }

        $onlyClient = 1 === \count($config['clients']);

        foreach ($config['clients'] as $name => $client) {
            $this->loadClient($name, $config['default_client'] === $name, $onlyClient, $client, $container);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://sendinblue.com/schema/dic/api';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return \dirname(__DIR__).'/Resources/config/schema';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'sendinblue_api';
    }

    /**
     * @return array
     */
    public static function getEndpoints()
    {
        return \array_keys(self::$MAPPING);
    }

    /**
     * @param string           $name
     * @param bool             $default
     * @param bool             $only
     * @param array            $client
     * @param ContainerBuilder $container
     */
    private function loadClient($name, $default, $only, array $client, ContainerBuilder $container)
    {
        $definitionClassName = $this->getDefinitionClassname();

        $configurationService = \sprintf('sendinblue_api.%s_client.configuration', $name);
        $configuration = $container->setDefinition(
            $configurationService,
            new $definitionClassName('sendinblue_api.client.configuration')
        );

        $configuration->addMethodCall('setApiKey', ['api-key', $client['key']]);

        $clientService = \sprintf('sendinblue_api.%s_client', $name);
        $container
            ->setDefinition($clientService, new $definitionClassName('sendinblue_api.client'))
            ->setArguments([new Reference($configurationService)])
        ;

        foreach ($client['endpoints'] as $endpoint) {
            $endpointService = \sprintf('sendinblue_api.%s_client.%s_endpoint', $name, $endpoint);

            $container
                ->setDefinition(
                    $endpointService,
                    new Definition(self::$MAPPING[$endpoint], [new Reference($clientService)])
                )
                ->setPublic(true)
            ;

            if ($default) {
                $container->setAlias(
                    \sprintf('sendinblue_api.%s_endpoint', $endpoint),
                    new Alias($endpointService, true)
                );
            }

            if ($only) {
                $container->setAlias(self::$MAPPING[$endpoint], $endpointService);
            }
        }
    }

    /**
     * @return string
     */
    private function getDefinitionClassname()
    {
        return \class_exists('Symfony\Component\DependencyInjection\ChildDefinition')
            ? 'Symfony\Component\DependencyInjection\ChildDefinition'
            : 'Symfony\Component\DependencyInjection\DefinitionDecorator'
        ;
    }
}
