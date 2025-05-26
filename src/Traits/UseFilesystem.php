<?php

namespace CBSOpenData\Traits;

use CBSOpenData\OpenData;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

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
            $this->setFilesystem(new Filesystem(new LocalFilesystemAdapter(OpenData::cachePath())));
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