<?php

namespace MauticPlugin\MauticSendinblueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * CREATE TABLE `dwh_stats` (
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
 *   `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *   `campaign_id` int(11) DEFAULT NULL,
 *   `campaign_category_id` int(11) DEFAULT NULL,
 *   `channel_id` int(11) DEFAULT NULL,
 *   `channel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *   `event_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 *   `event_ts` datetime DEFAULT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC
 *
 * // Update
 * ALTER TABLE dwh_stats ADD player_id player_id varchar(255) AFTER username
 *
*/
class DwhStats
{
    const CHANNEL_EMAIL = 'email';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $playerId;

    /**
     * @var int
     */
    private $channelId;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var int
     */
    private $campaignId;

    /**
     * @var int
     */
    private $campaignCategoryId;

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
        $builder->setTable('dwh_stats');

        $builder->addId();
        $builder->addField('username', 'string');
        $builder->addNamedField('playerId', 'string', 'player_id');
        $builder->addNamedField('campaignId', 'integer', 'campaign_id');
        $builder->addNamedField('campaignCategoryId', 'integer', 'campaign_category_id');
        $builder->addNamedField('channelId', 'integer', 'channel_id');
        $builder->addNamedField('channel', 'string', 'channel');
        $builder->addNamedField('eventType', 'string', 'event_type');
        $builder->createField('eventTs', 'datetime')
            ->columnName('event_ts')
            ->build();
    }

    /**
     * @return int
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
     * @return string
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param string $username
     *
     * @return DwhStats
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $playerId
     *
     * @return DwhStats
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

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
     * @return DwhStats
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCampaignCategoryId()
    {
        return $this->campaignCategoryId;
    }

    /**
     * @param int $campaignCategoryId
     *
     * @return DwhStats
     */
    public function setCampaignCategoryId($campaignCategoryId)
    {
        $this->campaignCategoryId = $campaignCategoryId;

        return $this;
    }

    /**
     * @return int
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * @param int $channelId
     *
     * @return DwhStats
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     *
     * @return DwhStats
     */
    public function setChannel($channel = self::CHANNEL_EMAIL)
    {
        $this->channel = $channel;

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
     * @return DwhStats
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
     * @return DwhStats
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }
}
