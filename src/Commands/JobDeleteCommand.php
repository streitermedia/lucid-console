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

use Lucid\Console\Str;
use Lucid\Console\Finder;
use Lucid\Console\Command;
use Lucid\Console\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
class JobDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'delete:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an existing Job in a domain';

    /**
     * The type of class being deleted.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $domain = \Illuminate\Support\Str::studly($this->argument('domain'));
            $title = $this->parseName($this->argument('job'));

            if (!$this->exists($job = $this->findJobPath($domain, $title))) {
                $this->error('Job class '.$title.' cannot be found.');
            } else {
                $this->delete($job);

                if (count($this->listJobs($domain)->first()) === 0) {
                    $this->delete($this->findDomainPath($domain));
                }

                $this->info('Job class <comment>'.$title.'</comment> deleted successfully.');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments()
    {
        return [
            ['job', InputArgument::REQUIRED, 'The job\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain from which the job will be deleted.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../Generators/stubs/job.stub';
    }

    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::job($name);
    }
}
