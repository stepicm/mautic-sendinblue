<?php

namespace MauticPlugin\MauticSendinblueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class CustomSimpleCampaign
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $categoryId;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('campaigns');

        $builder->addId();
        $builder->addNamedField('categoryId', 'integer', 'category_id');
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
    public function getCategoryId()
    {
        return $this->categoryId;
    }
}
