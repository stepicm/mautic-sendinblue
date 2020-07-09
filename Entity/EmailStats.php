<?php

namespace MauticPlugin\MauticSendinblueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class EmailStats
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $emailId;

    /**
     * @var int
     */
    private $leadId;

    /**
     * @var int
     */
    private $listId;

    /**
     * @var int
     */
    private $ipId;

    /**
     * @var string
     */
    private $copyId;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var \DateTime
     */
    private $dateSent;

    /**
     * @var bool
     */
    private $isRead;

    /**
     * @var bool
     */
    private $isFailed;

    /**
     * @var bool
     */
    private $viewedInBrowser;

    /**
     * @var \DateTime
     */
    private $dateRead;

    /**
     * @var string
     */
    private $trackingHash;

    /**
     * @var int
     */
    private $retryCount;

    /**
     * @var string
     */
    private $source;

    /**
     * @var int
     */
    private $sourceId;

    /**
     * @var string
     */
    private $tokens;

    /**
     * @var int
     */
    private $openCount;

    /**
     * @var \DateTime
     */
    private $lastOpened;

    /**
     * @var string
     */
    private $openDetails;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('email_stats');

        $builder->addId();
        $builder->addNamedField('emailId', 'integer', 'email_id');
        $builder->addNamedField('leadId', 'integer', 'lead_id');
        $builder->addNamedField('listId', 'integer', 'list_id');
        $builder->addNamedField('ipId', 'integer', 'ip_id');
        $builder->addNamedField('copyId', 'string', 'copy_id');
        $builder->addNamedField('emailAddress', 'string', 'email_address');
        $builder->addNamedField('dateSent', 'datetime', 'date_sent');
        $builder->addNamedField('isRead', 'boolean', 'is_read');
        $builder->addNamedField('isFailed', 'boolean', 'is_failed');
        $builder->addNamedField('viewedInBrowser', 'boolean', 'viewed_in_browser');
        $builder->addNamedField('dateRead', 'datetime', 'date_read');
        $builder->addNamedField('trackingHash', 'string', 'tracking_hash');
        $builder->addNamedField('retryCount', 'integer', 'retry_count');
        $builder->addNamedField('source', 'string', 'source');
        $builder->addNamedField('sourceId', 'integer', 'source_id');
        $builder->addNamedField('tokens', 'text', 'tokens');
        $builder->addNamedField('openCount', 'integer', 'open_count');
        $builder->addNamedField('lastOpened', 'datetime', 'last_opened');
        $builder->addNamedField('openDetails', 'text', 'open_details');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLeadId()
    {
        return $this->leadId;
    }

    /**
     * @return int
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @return int
     */
    public function getEmailId()
    {
        return $this->emailId;
    }
}
