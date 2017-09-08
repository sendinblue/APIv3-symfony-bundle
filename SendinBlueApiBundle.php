<?php

namespace SendinBlue\Bundle\ApiBundle;

use SendinBlue\Bundle\ApiBundle\DependencyInjection\SendinBlueApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SendinBlueApiBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new SendinBlueApiExtension();
    }
}
