<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Command;

use Common\Core\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('news-agency:debug')
            ->setDescription('Debugs XML file parsing for news agencies')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The XML file to parse'
            );

        $this->steps[] = 6;
    }

    /**
     * {@inheritdoc}
     */
    protected function do()
    {
        $file    = $this->input->getArgument('file');
        $factory = $this->getContainer()->get('news_agency.factory.parser');

        $this->writeStep('Checking file');

        if (!file_exists($file)) {
            $this->writeStatus('error', 'FAIL');
            $this->writeStatus('error', " (No such file or directory: $file)", true);
            return;
        }

        $this->writeStatus('success', 'DONE', true);
        $this->writeStep('Loading XML from file');
        $xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOERROR);

        if (empty($xml)) {
            $this->writeStatus('error', 'FAIL (No valid XML)', true);
            return;
        }

        $this->writeStatus('success', 'DONE', true);
        $this->writeStep('Searching a parser');

        try {
            $parser = $factory->get($xml);
            $this->writeStatus('success', 'DONE');
            $this->writeStatus('info', sprintf(' (%s)', get_class($parser)), true);

            $this->writeStep('Parsing file');
            $parsed = $parser->parse($xml, $file);
            $this->writeStatus('success', 'DONE', true);
        } catch (\Exception $e) {
            $this->writeStatus('error', sprintf('FAIL (%s)', $e->getMessage()), true);
        }

        if ($this->output->isVerbose()) {
            \Symfony\Component\VarDumper\VarDumper::dump($parsed);
        }
    }
}
