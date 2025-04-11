<?php declare(strict_types=1);


use Shopware\Core\TestBootstrapper;

$loader = (new TestBootstrapper())
    ->addCallingPlugin()
    ->addActivePlugins('LoyaltyProgram')
    ->setForceInstallPlugins(true)
    ->bootstrap()
    ->getClassLoader();

$loader->addPsr4('LoyaltyProgram\\Tests\\', __DIR__);
