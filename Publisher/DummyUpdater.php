<?php

namespace MauticPlugin\MauticSendinblueBundle\Publisher;

use MauticPlugin\MauticSendinblueBundle\Publisher\Data\DataUpdater;

class DummyUpdater implements UpdaterInterface
{
    /**
     * @param DataUpdater $data
     * @return bool
     */
    public function send(DataUpdater $data)
    {
        return true;
    }
}
