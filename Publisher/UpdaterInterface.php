<?php

namespace MauticPlugin\MauticSendinblueBundle\Publisher;

use MauticPlugin\MauticSendinblueBundle\Publisher\Data\DataUpdater;

interface UpdaterInterface
{
    /**
     * @param DataUpdater $data
     * @return bool
     */
    public function send(DataUpdater $data);
}
