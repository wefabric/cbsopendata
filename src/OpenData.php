<?php


namespace CBSOpenData;


class OpenData
{
    /**
     * @return string
     */
    public static function rootPath(): string
    {
        return __DIR__.'/../';
    }

    /**
     * @return string
     */
    public static function cachePath(): string
    {
        return self::rootPath().'cache';
    }
}