<?php

/*
 * This file is part of the lucid-console project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucid\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class NewCommand extends Command
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Lucid-architected project')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('laravel', null, InputOption::VALUE_NONE, 'Specify the Laravel version you wish to install');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->verifyApplicationDoesntExist(
            $directory = ($input->getArgument('name')) ? getcwd().'/'.$input->getArgument('name') : getcwd(),
            $output
        );

        $output->writeln('<info>Crafting Lucid application...</info>');

        /*
         * @TODO: Get Lucid based on the Laravel version.
         */
        $process = new Process([$this->findComposer(), ' create-project laravel/laravel ' . $directory]);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $output->writeln('<comment>Application ready! Make your dream a reality.</comment>');
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param string $directory
     * @param OutputInterface $output
     */
    protected function verifyApplicationDoesntExist($directory, OutputInterface $output)
    {
        if ($directory !== getcwd() && (is_dir($directory) || is_file($directory))) {
            throw new \RuntimeException('Application already exists!');
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        $composer = 'composer';

        if (file_exists(getcwd().'/composer.phar')) {
            $composer = '"'.PHP_BINARY.'" composer.phar';
        }

        return $composer;
    }
}
