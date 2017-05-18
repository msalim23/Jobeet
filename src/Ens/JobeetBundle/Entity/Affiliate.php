<?php

namespace Ens\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Affiliate
 *
 * @ORM\Table(name="affiliate")
 * @ORM\Entity(repositoryClass="Ens\JobeetBundle\Repository\AffiliateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Affiliate
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creadted_at", type="datetime", nullable=true)
     */
    private $creadtedAt;
    
     /**
      * @var array
      * 
      * @ORM\OneToMany(targetEntity="CategoryAffiliate", mappedBy="affiliateId")
      */
     private $category_affiliates;
    
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
     * Set url
     *
     * @param string $url
     *
     * @return Affiliate
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Affiliate
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Affiliate
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Affiliate
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set creadtedAt
     *
     * @param \DateTime $creadtedAt
     *
     * @return Affiliate
     */
    public function setCreadtedAt($creadtedAt)
    {
        $this->creadtedAt = $creadtedAt;

        return $this;
    }

    /**
     * Get creadtedAt
     *
     * @return \DateTime
     */
    public function getCreadtedAt()
    {
        return $this->creadtedAt;
    }
    
   	/**
   	 * @ORM\PrePersist
   	 */
   	public function setCreatedAtValue()
   	{
   	  $this->createdAt = new \DateTime();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category_affiliates = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add categoryAffiliate
     *
     * @param \Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate
     *
     * @return Affiliate
     */
    public function addCategoryAffiliate(\Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate)
    {
        $this->category_affiliates[] = $categoryAffiliate;

        return $this;
    }

    /**
     * Remove categoryAffiliate
     *
     * @param \Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate
     */
    public function removeCategoryAffiliate(\Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate)
    {
        $this->category_affiliates->removeElement($categoryAffiliate);
    }

    /**
     * Get categoryAffiliates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryAffiliates()
    {
        return $this->category_affiliates;
    }
}
