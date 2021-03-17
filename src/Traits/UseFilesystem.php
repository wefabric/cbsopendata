<?php

namespace CBSOpenData\Traits;

use CBSOpenData\OpenData;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

trait UseFilesystem
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        if(!$this->filesystem) {
            $this->setFilesystem(new Filesystem(new Local(OpenData::cachePath())));
        }

        return $this->filesystem;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

}