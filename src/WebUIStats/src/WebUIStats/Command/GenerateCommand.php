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

namespace WebUIStats\Command;

use Symfony\Component\Console\Input\InputArgument,
Symfony\Component\Console\Input\InputOption,
Symfony\Component\Console\Command\Command,
Symfony\Component\Console\Input\InputInterface,
Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    /**
     * Configure command, set parameters definition and help.
     */
    protected function configure()
    {
        $this
            ->setName('interface:generate')
            ->setDescription('Generate js interface.')
            ->setDefinition(array(
            new InputArgument('config', InputArgument::REQUIRED, 'yml config'),
        ))
            ->setHelp(sprintf(
            '%sGenerate js interface.%s',
            PHP_EOL,
            PHP_EOL
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yml_config = $input->getArgument('config');

        //    $output->writeln("config: " . $yml_config);

        $creator = new \WebUIStats\Generator\Creator($output, $yml_config);
        $creator->create();
    }
}