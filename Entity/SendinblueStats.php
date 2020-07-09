<?php

namespace MauticPlugin\MauticSendinblueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class SendinblueStats
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var int
     */
    private $emailId;

    /**
     * @var int
     */
    private $campaignId;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var \DateTime
     */
    private $eventTs;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('sendinblue_email_stats');

        $builder->addId();
        $builder->addField('username', 'string');

        $builder->addNamedField('emailId', 'int', 'email_id');
        $builder->addNamedField('campaignId', 'int', 'campaign_id');
        $builder->addNamedField('categoryId', 'int', 'category_id');

        $builder->createField('eventTs', 'datetime')
            ->columnName('event_ts')
            ->build();

        $builder->addNamedField('eventType', 'string', 'event_type');
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return SendinblueStats
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return int
     */
    public function getEmailId()
    {
        return $this->emailId;
    }

    /**
     * @param int $emailId
     *
     * @return SendinblueStats
     */
    public function setEmailId(int $emailId)
    {
        $this->emailId = $emailId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @param int $campaignId
     *
     * @return SendinblueStats
     */
    public function setCampaignId(int $campaignId)
    {
        $this->campaignId = $campaignId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     *
     * @return SendinblueStats
     */
    public function setCategoryId(int $categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEventTs()
    {
        return $this->eventTs;
    }

    /**
     * @param \DateTime $eventTs
     *
     * @return SendinblueStats
     */
    public function setEventTs($eventTs)
    {
        $this->eventTs = $eventTs;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     * 
     * @return SendinblueStats
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }
}
