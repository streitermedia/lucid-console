<?php

/*
 * This file is part of the lucid-console project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucid\Console\Components;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Service extends Component
{
    public function __construct($name, $realPath, $relativePath)
    {
        $this->setAttributes([
            'name' => $name,
            'slug' => \Illuminate\Support\Str::snake($name),
            'realPath' => $realPath,
            'relativePath' => $relativePath,
        ]);
    }
}
