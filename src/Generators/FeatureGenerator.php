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
use Lucid\Console\Components\Feature;
use Lucid\Console\Generators\Traits\GeneratorHelperTrait;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class FeatureGenerator extends Generator
{
    use GeneratorHelperTrait;

    public function generate($feature, $service, array $jobs = [])
    {
        $feature = Str::feature($feature);
        $service = Str::service($service);

        $path = $this->findFeaturePath($service, $feature);

        if ($this->exists($path)) {
            throw new Exception('Feature already exists!');

            return false;
        }

        $namespace = $this->findFeatureNamespace($service);

        $content = file_get_contents($this->getStub());

        [$useJobs, $runJobs] = $this->createJobStrings($jobs);

        $content = str_replace(
            ['{{feature}}', '{{namespace}}', '{{foundation_namespace}}', '{{use_jobs}}', '{{run_jobs}}'],
            [$feature, $namespace, $this->findFoundationNamespace(), $useJobs, $runJobs],
            $content
        );

        $this->createFile($path, $content);

        // generate test file
        $this->generateTestFile($feature, $service);

        return new Feature(
            $feature,
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
     * @param string $feature
     * @param string $service
     *
     * @throws Exception
     */
    private function generateTestFile($feature, $service)
    {
    	$content = file_get_contents($this->getTestStub());

    	$namespace = $this->findFeatureTestNamespace($service);
        $featureNamespace = $this->findFeatureNamespace($service)."\\$feature";
        $testClass = $feature.'Test';

    	$content = str_replace(
    		['{{namespace}}', '{{testclass}}', '{{feature}}', '{{feature_namespace}}'],
    		[$namespace, $testClass, mb_strtolower($feature), $featureNamespace],
    		$content
    	);

    	$path = $this->findFeatureTestPath($service, $testClass);

    	$this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/feature.stub';
    }

    /**
     * Get the test stub file for the generator.
     *
     * @return string
     */
    private function getTestStub()
    {
    	return __DIR__.'/stubs/feature-test.stub';
    }
}
