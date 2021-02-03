<?php

namespace MauticPlugin\MauticSendinblueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * CREATE TABLE `sendinblue_leads` (
 * `id` int(11) NOT NULL AUTO_INCREMENT,
 * `sendinblue_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 * `lead_hash_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 * PRIMARY KEY (`id`),
 * KEY `sendinblue_id` (`sendinblue_id`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=947424 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC
 */

class SendinblueHash
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $leadHashId;

    /**
     * @var string
     */
    private $sendinblueId;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('sendinblue_leads')
                ->addIndex(['sendinblue_id'], 'sendinblue_id');

        $builder->addId();
        $builder->addNamedField('sendinblueId', 'string', 'sendinblue_id');
        $builder->addNamedField('leadHashId', 'string', 'lead_hash_id');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLeadHashId()
    {
        return $this->leadHashId;
    }

    /**
     * @param string $leadHashId
     *
     * @return SendinblueHash
     */
    public function setLeadHashId($leadHashId)
    {
        $this->leadHashId = $leadHashId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSendinblueId()
    {
        return $this->sendinblueId;
    }

    /**
     * @param string $sendinblueId
     *
     * @return SendinblueHash
     */
    public function setSendinblueId($sendinblueId)
    {
        $this->sendinblueId = $sendinblueId;

        return $this;
    }
}

