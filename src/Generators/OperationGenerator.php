<?php

/*
 * This file is part of the lucid-console project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucid\Console\Generators;

use Exception;
use Lucid\Console\Str;
use Lucid\Console\Components\Operation;
use Lucid\Console\Generators\Traits\GeneratorHelperTrait;

/**
 * @author Ali Issa <ali@vinelab.com>
 */
class OperationGenerator extends Generator
{
    use GeneratorHelperTrait;

    public function generate($operation, $service, $isQueueable = false, array $jobs = [])
    {
        $operation = Str::operation($operation);
        $service = Str::service($service);

        $path = $this->findOperationPath($service, $operation);

        if ($this->exists($path)) {
            throw new \ErrorException('Operation already exists!');

            return false;
        }

        $namespace = $this->findOperationNamespace($service);

        $content = file_get_contents($this->getStub($isQueueable));

        [$useJobs, $runJobs] = $this->createJobStrings($jobs);

        $content = str_replace(
            ['{{operation}}', '{{namespace}}', '{{foundation_namespace}}', '{{use_jobs}}', '{{run_jobs}}'],
            [$operation, $namespace, $this->findFoundationNamespace(), $useJobs, $runJobs],
            $content
        );

        $this->createFile($path, $content);

        // generate test file
        $this->generateTestFile($operation, $service);

        return new Operation(
            $operation,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($service) ? $this->findService($service) : null,
            $content
        );
    }

    /**
     * Generate the test file.
     *
     * @param string $operation
     * @param string $service
     *
     * @throws Exception
     */
    private function generateTestFile($operation, $service)
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findOperationTestNamespace($service);
        $operationNamespace = $this->findOperationNamespace($service)."\\$operation";
        $testClass = $operation.'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{operation}}', '{{operation_namespace}}'],
            [$namespace, $testClass, mb_strtolower($operation), $operationNamespace],
            $content
        );

        $path = $this->findOperationTestPath($service, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     *
     * @param bool $isQueueable
     *
     * @return string
     */
    protected function getStub($isQueueable = false)
    {
        return $this->getStubSelector(
            '/stubs/queueable-operation.stub',
            '/stubs/operation.stub',
            $isQueueable
        );
    }

    /**
     * Get the test stub file for the generator.
     *
     * @return string
     */
    private function getTestStub()
    {
        return __DIR__.'/stubs/operation-test.stub';
    }
}
