<?php


namespace Lucid\Console\Generators\Traits;


trait GeneratorHelperTrait
{
    /**
     * Create a collection of job statements.
     *
     * @param $jobs
     *
     * @return array
     */
    private function createJobStrings($jobs)
    {
        $useJobs = ''; // stores the `use` statements of the jobs
        $runJobs = ''; // stores the `$this->run` statements of the jobs

        foreach ($jobs as $index => $job) {
            $useJobs .= 'use '.$job['namespace'].'\\'.$job['className'].";\n";
            $runJobs .= "\t\t".'$this->run('.$job['className'].'::class);';

            // only add carriage returns when it's not the last job
            if ($index !== count($jobs) - 1) {
                $runJobs .= "\n\n";
            }
        }

        return [$useJobs, $runJobs];
    }

    /**
     * Get the stub based on $isQueueable.
     *
     * @param $onTrue
     * @param $onFalse
     * @param bool $isQueueable
     *
     * @return string
     */
    private function getStubSelector($onTrue, $onFalse, $isQueueable = false)
    {
        return __DIR__ . ($isQueueable ? $onTrue : $onFalse);
    }
}
