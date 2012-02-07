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

namespace WebUIStats;

use Symfony\Component\Console\Application,
WebUIStats\Command;

class App extends Application
{
    public function __construct()
    {
        parent::__construct('Welcome to stats charts interface generator', '0.1');

        $this->addCommands(array(
            new Command\GenerateCommand(),
        ));
    }
}
