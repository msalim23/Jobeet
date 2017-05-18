<?php

namespace Ens\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryAffiliate
 *
 * @ORM\Table(name="category_affiliate")
 * @ORM\Entity(repositoryClass="Ens\JobeetBundle\Repository\CategoryAffiliateRepository")
 */
class CategoryAffiliate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="category_affiliates")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $categoryId;
    
    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Affiliate", inversedBy="category_affiliates")
     * @ORM\JoinColumn(name="affiliate_id", referencedColumnName="id")
     */
    private $affiliateId;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param \Ens\JobeetBundle\Entity\Category $category
     *
     * @return CategoryAffiliate
     */
    public function setCategoryId(\Ens\JobeetBundle\Entity\Category $category = null)
    {
        $this->categoryId = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Ens\JobeetBundle\Entity\Category
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set affiliate
     *
     * @param \Ens\JobeetBundle\Entity\Affiliate $affiliate
     *
     * @return CategoryAffiliate
     */
    public function setAffiliateId(\Ens\JobeetBundle\Entity\Affiliate $affiliate = null)
    {
        $this->affiliateId = $affiliate;

        return $this;
    }

    /**
     * Get affiliate
     *
     * @return \Ens\JobeetBundle\Entity\Affiliate
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }
}
