<?php

namespace MauticPlugin\MauticSendinblueBundle\Publisher\Data;

class DataUpdater
{
    const DATA_EVENT = 'event';
    const DATA_SB_ID = 'sb_id';
    const DATA_URL = 'url';
    const DATA_EMAIL = 'email';
    const DATA_HASH_ID = 'hash_id';
    const DATA_ACTION = 'action';

    /**
     * @var array
     */
    protected $mandatory = [
        self::DATA_EVENT,
        self::DATA_SB_ID,
    ];

    /**
     * @var array
     */
    protected $optional = [
        self::DATA_HASH_ID,
        self::DATA_EMAIL,
        self::DATA_ACTION,
        self::DATA_URL,
    ];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     * @return void
     * @throws \RuntimeException
     */
    protected function validate(array $data)
    {
        foreach ($this->mandatory as $check) {
            if (false === isset($data[$check])) {
                throw new \RuntimeException('Mandatory data parameter is missing!');
            }
        }

        foreach ($data as $check => $value) {
            if (false === in_array($check, $this->mandatory) && false === in_array($check, $this->optional)) {
                throw new \RuntimeException('Optional data parameter not recognised!');
            }
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->validate($data);
        $this->data = $data;
    }

    /**
     * @param string|null $param
     * @return array
     */
    public function getData($param = null)
    {
        if ($param !== null) {
            return [
                $param => $this->data[$param],
            ];
        }
        return $this->data;
    }
}
