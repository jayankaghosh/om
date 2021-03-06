<?php

require_once __DIR__ . '/../common.php';

$config = Om\DiConfig\Config::fromXml(\file_get_contents(__DIR__  . '/di.xml'));
$config = Om\DiConfig\Config::fromXml(
    \file_get_contents(__DIR__  . '/di-extended.xml'),
    $config
);
$writablePath = __DIR__  . '/generated';
$objectManagerFactory = new \Om\OmFactory($config, $writablePath);
$objectManager = $objectManagerFactory->getInstance();

class Adder extends Calculator {
    /**
     * @var string
     */
    private $prefix;

    /**
     * Adder constructor.
     * @param string $prefix
     */
    public function __construct(
        string $prefix = ''
    )
    {
        $this->prefix = $prefix;
    }

    public function add($a, $b)
    {
        return $this->prefix . parent::add($a, $b);
    }
}

class AdderPlugin {
    public function afterAdd($subject, $result)
    {
        return $result . '. And this is the suffix added through plugin';
    }
}

/** @var Adder $adder */
$adder = $objectManager->get('my-adder-alias');
echo $adder->add(2, 3);