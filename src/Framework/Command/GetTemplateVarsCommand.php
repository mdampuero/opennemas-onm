<?php

namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GetTemplateVarsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('template:vars')
            ->setDescription('Get variables used in templates.')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'The source to check.'
            )
            ->addOption(
                'group',
                false,
                InputOption::VALUE_NONE,
                'If set, return all variables grouped by template'
            )
            ->addOption(
                'usage',
                false,
                InputOption::VALUE_NONE,
                'If set, find variables by usage'
            )

            ->setHelp(
                <<<EOF
The <info>theme:vars</info> finds all variables used in theme templates.
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getOption('group');
        $usage = $input->getOption('usage');
        $source = $input->getArgument('source');

        $files = $this->getFiles($source);

        $vars = $this->getVars($files, $usage);

        if (!$group) {
            $flat = [];

            foreach ($vars as $tpl) {
                $flat = array_merge($flat, $tpl);
            }

            $flat = array_unique($flat);
            sort($flat);

            foreach ($flat as $var) {
                $output->writeln($var);
            }
        } else {
            foreach ($vars as $tpl => $v) {
                $output->writeln("\n\nTemplate: " . $tpl);
                sort($v);
                $output->write("Vars: " . implode($v, ', '));
            }
        }
    }

    /**
     * Get all files to extract variables from.
     *
     * @param string $dir The base file or directory.
     *
     * @return array The array of files to check.
     */
    protected function getFiles($dir)
    {
        if (is_file($dir) && !preg_match('/.*\.tpl$/', $dir)) {
            return [];
        }

        if (!is_dir($dir) && is_file($dir) && preg_match('/.*\.tpl$/', $dir)) {
            return [ $dir ];
        }

        $files = array_diff(scandir($dir), array('..', '.'));

        $output = [];
        foreach ($files as $file) {
            $path = $dir . '/' . $file;

            if (is_dir($path)) {
                $output = array_merge($output, $this->getFiles($path));
            } elseif (is_file($path) && preg_match('/.*\.tpl$/', $path)) {
                $output[] = $path;
            }
        }

        return $output;
    }

    /**
     * Get all variables from files.
     *
     * @param array  $files The array of files where extract variables from.
     * @param string $usage Whether to check variable usage.
     *
     * @return array The array of variables.
     */
    protected function getVars($files, $usage)
    {
        $pattern = '/(\$[a-zA-Z]\w*)/';

        if ($usage) {
            $pattern = '/\$[a-zA-Z_]\w*\s*(\.|->|\[|\')*\s*\$*\w*(\]|\')*/';
        }

        $vars = [];
        foreach ($files as $file) {
            $path = $file;

            if (!is_dir($path)) {
                $content = file_get_contents($path);

                preg_match_all($pattern, $content, $matches);

                $vars[$path] = array_unique($matches[0]);
            }
        }

        return $vars;
    }
}
