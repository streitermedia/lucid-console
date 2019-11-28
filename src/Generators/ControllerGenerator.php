<?php

/*
 * This file is part of the laravel-console project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucid\Console\Generators;

use Exception;
use Lucid\Console\Str;
use Lucid\Console\Generators\Traits\GeneratorHelperTrait;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class ControllerGenerator extends Generator
{
    use GeneratorHelperTrait;

    public function generate($name, $service, $plain = false)
    {
        $name = Str::controller($name);
        $service = Str::service($service);

        $path = $this->findControllerPath($service, $name);

        if ($this->exists($path)) {
            throw new \ErrorException('Controller already exists!');
        }

        $namespace = $this->findControllerNamespace($service);

        $content = file_get_contents($this->getStub($plain));
        $content = str_replace(
             ['{{controller}}', '{{namespace}}', '{{foundation_namespace}}'],
             [$name, $namespace, $this->findFoundationNamespace()],
             $content
         );

        $this->createFile($path, $content);

        return $this->relativeFromReal($path);
    }

    /**
     * Get the stub file for the generator.
     *
     * @param bool $plain
     *
     * @return string
     */
    protected function getStub($plain)
    {
        return $this->getStubSelector(
            '/stubs/controller.plain.stub',
            '/stubs/controller.stub',
            $plain
        );
    }
}
