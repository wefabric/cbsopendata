<?php


namespace CBSOpenData;


class OpenData
{
    public static function rootPath()
    {
        return __DIR__.'/../';
    }

    public static function cachePath()
    {
        return self::rootPath().'cache';
    }
}