<?php

namespace MauticPlugin\MauticSendinblueBundle\Publisher;

use MauticPlugin\MauticSendinblueBundle\Publisher\Data\DataUpdater;

class UpdateRunner
{
    protected $classes = [];
    protected $result = [];

    public function __construct()
    {
        foreach (glob(__DIR__ . '/*Updater.php') as $class) {
            require_once $class;
            $this->classes[] = preg_replace('/.*\/(.*)\..*/', '$1', $class);
        }
    }

    /**
     * @param DataUpdater $data
     * @return array
     */
    public function send(DataUpdater $data)
    {
        $namespace = 'MauticPlugin\\MauticSendinblueBundle\\Publisher\\';

        foreach ($this->classes as $class) {
            $classname = $namespace . $class;
            $this->result[] = (new $classname())->send($data);
        }

        return $this->result;
    }
}
