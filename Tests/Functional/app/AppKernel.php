<?php

namespace SendinBlue\Bundle\ApiBundle\Tests\Functional\app;

use SendinBlue\Bundle\ApiBundle\SendinBlueApiBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * @var string
     */
    private $config;

    /**
     * @param string $config configuration filename
     */
    public function __construct($config)
    {
        parent::__construct(\uniqid(), false);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new SendinBlueApiBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return \sys_get_temp_dir().'/sendinblue/api-bundle/cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return \sys_get_temp_dir().'/sendinblue/api-bundle/logs';
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/'.$this->config);
    }
}
