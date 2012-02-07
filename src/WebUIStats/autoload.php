<?php

/*
 * This file is part of the WebUIStats package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
//require_once __DIR__.'/vendor/Symfony/Component/ClassLoader/DebugUniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

//$loader = new DebugUniversalClassLoader();
$loader = new UniversalClassLoader();

// register classes with namespaces
$loader->registerNamespaces(array(
                                 'WebUIStats' => __DIR__ . "/src/",
                                 'Symfony' => __DIR__ . "/vendor/",
                            ));
$loader->registerPrefixes(array(
                               'Pimple' => __DIR__ . '/vendor/pimple/lib',
                          ));
$loader->register();


