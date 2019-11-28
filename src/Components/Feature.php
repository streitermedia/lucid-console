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
class Feature extends Component
{
    public function __construct($title, $file, $realPath, $relativePath, Service $service = null, $content = '')
    {
        $className = str_replace(' ', '', $title).'Feature';

        $this->setAttributes([
            'title' => $title,
            'className' => $className,
            'service' => $service,
            'file' => $file,
            'realPath' => $realPath,
            'relativePath' => $relativePath,
            'content' => $content,
        ]);
    }
}
