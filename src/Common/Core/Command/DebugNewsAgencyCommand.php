<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Command;

use Framework\Import\ParserFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DebugNewsAgencyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:news-agency:debug')
            ->setDescription('Debugs XML file parsing for news agencies')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The XML file to parse'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $sf   = new ParserFactory();

        if (!file_exists($file)) {
            throw new \Exception('Unable to find file ' . $file);
        }

        $xml = simplexml_load_file($file);

        $parser = $sf->get($xml);
        $parsed = $parser->parse($xml, $file);

        if ($output->isVerbose()) {
            \Symfony\Component\VarDumper\VarDumper::dump($parsed);
        }
    }
}
