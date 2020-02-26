<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\DeepConfig;

class TwigExtension extends AbstractExtension
{
    const DEEP_CONFIG_FUNC_NAME = 'conf';

    private $deepConfig;

    public function __construct(DeepConfig $deepConfig)
    {
        $this->deepConfig = $deepConfig;
    }

    public function conf($key)
    {
        return $this->deepConfig->get($key);
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(self::DEEP_CONFIG_FUNC_NAME, [$this, 'conf']),
        ];
    }
}