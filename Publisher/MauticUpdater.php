<?php

namespace MauticPlugin\MauticSendinblueBundle\Publisher;

use MauticPlugin\MauticSendinblueBundle\Publisher\Data\DataUpdater;

class MauticUpdater implements UpdaterInterface
{
    const HTTP_OK = 200;
    const HTTP_REDIRECT = 302;

    /**
     * @param DataUpdater $data
     * @return bool
     */
    public function send(DataUpdater $data)
    {
        $param = $data->getData();

        if (isset($param[DataUpdater::DATA_URL]) && $param[DataUpdater::DATA_URL] !== false) {
            try {
                $this->getMauticUrl($param[DataUpdater::DATA_URL]);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        }

        if (isset($param[DataUpdater::DATA_ACTION])) {
            return $this->postMauticAction($param[DataUpdater::DATA_ACTION]);
        }
    }

    /**
     * @param string $url
     * @return void
     * @throws \Exception
     */
    protected function getMauticUrl($url)
    {
        $res = curl_init();
        $opt = [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
        ];

        curl_setopt_array($res, $opt);

        $result = curl_exec($res);
        $httpcode = curl_getinfo($res, CURLINFO_HTTP_CODE);

        // we have error
        if ($httpcode !== self::HTTP_OK && $httpcode !== self::HTTP_REDIRECT) {
            throw new \Exception($result, $httpcode);
        }

        curl_close($res);

        return $result;
    }

    // this is be used to filter out mautic un/re subscribe links
    // later will be used to send data to BQ
    // in special class (of course)
    protected function postMauticAction($action)
    {
        return $action;
    }
}
