<?php

namespace Ens\JobeetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Level
 *
 * @ORM\Table(name="level")
 * @ORM\Entity(repositoryClass="Ens\JobeetBundle\Repository\LevelRepository")
 */
class Level
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
     *
     * @var array @ORM\OneToMany(targetEntity="Job", mappedBy="LevelId")
     */
    private $jobs;

    /**
     * @var string
     * @Assert\Length(max = 10)
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    
    public function getJobs (){
        
        return $this->jobs;
    
    }
    
    public function setJobs($jobs){
        
        $this->jobs = $jobs;
        
        return $this;
    }

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Level
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }
}

