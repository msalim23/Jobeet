<?php

namespace Ens\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ens\JobeetBundle\Utils\Jobeet;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Ens\JobeetBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category {
	/**
	 *
	 * @var int @ORM\Column(name="id", type="integer")
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 *
	 * @var string @ORM\Column(name="name", type="string", length=255, unique=true)
	 */
	private $name;
	
	/**
	 *
	 * @var array @ORM\OneToMany(targetEntity="Job", mappedBy="categoryId")
	 */
	private $jobs;
	
	/**
	 *
	 * @var array @ORM\OneToMany(targetEntity="CategoryAffiliate", mappedBy="categoryId")
	 */
	private $category_affiliates;
	
	/**
	 *
	 * @var string $slug @ORM\Column(name="slug", type="string", length=255, unique=true)
	 */
	private $slug;
	
	private $active_jobs;
	
	private $more_jobs;
	
	/**
	 * Set slug
	 *
	 * @param string $slug        	
	 */
	public function setSlug($slug) {
		$this->slug = $slug;
	}
	/**
	 * Get slug
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}
	/**
	 * @ORM\prePersist
	 */
	public function setSlugValue() {
		$this->slug = Jobeet::slugify($this->getName());
	}
	public function setActiveJobs($jobs) {
		$this->active_jobs = $jobs;
	}
	public function getActiveJobs() {
		return $this->active_jobs;
	}
	
	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set name
	 *
	 * @param string $name        	
	 *
	 * @return Category
	 */
	public function setName($name) {
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	public function __toString() {
		return $this->getName ();
	}
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->jobs = new \Doctrine\Common\Collections\ArrayCollection ();
		$this->category_affiliates = new \Doctrine\Common\Collections\ArrayCollection ();
	}
	
	/**
	 * Add job
	 *
	 * @param \Ens\JobeetBundle\Entity\Job $job        	
	 *
	 * @return Category
	 */
	public function addJob(\Ens\JobeetBundle\Entity\Job $job) {
		$this->jobs [] = $job;
		
		return $this;
	}
	
	/**
	 * Remove job
	 *
	 * @param \Ens\JobeetBundle\Entity\Job $job        	
	 */
	public function removeJob(\Ens\JobeetBundle\Entity\Job $job) {
		$this->jobs->removeElement ( $job );
	}
	
	/**
	 * Get jobs
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getJobs() {
		return $this->jobs;
	}
	
	/**
	 * Add categoryAffiliate
	 *
	 * @param \Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate        	
	 *
	 * @return Category
	 */
	public function addCategoryAffiliate(\Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate) {
		$this->category_affiliates [] = $categoryAffiliate;
		
		return $this;
	}
	
	/**
	 * Remove categoryAffiliate
	 *
	 * @param \Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate        	
	 */
	public function removeCategoryAffiliate(\Ens\JobeetBundle\Entity\CategoryAffiliate $categoryAffiliate) {
		$this->category_affiliates->removeElement ( $categoryAffiliate );
	}
	
	/**
	 * Get categoryAffiliates
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCategoryAffiliates() {
		return $this->category_affiliates;
	}

	public function setMoreJobs($jobs) {
		$this->more_jobs = $jobs >= 0 ? $jobs : 0;
	}
	public function getMoreJobs() {
		return $this->more_jobs;
	}
}
