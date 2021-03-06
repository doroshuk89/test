<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Twig\\' => array($vendorDir . '/twig/twig/src'),
    'Symfony\\Polyfill\\Mbstring\\' => array($vendorDir . '/symfony/polyfill-mbstring'),
    'Symfony\\Polyfill\\Ctype\\' => array($vendorDir . '/symfony/polyfill-ctype'),
    'Psr\\Container\\' => array($vendorDir . '/psr/container/src'),
    'Model\\' => array($baseDir . '/app/Model'),
    'Middleware\\' => array($baseDir . '/app/Core/Middleware'),
    'Library\\' => array($baseDir . '/library'),
    'Interfaces\\' => array($baseDir . '/app/Interfaces'),
    'CustomException\\' => array($baseDir . '/app/Core/Exceptions'),
    'Core\\' => array($baseDir . '/app/Core'),
    'Controller\\' => array($baseDir . '/app/Controller'),
);
