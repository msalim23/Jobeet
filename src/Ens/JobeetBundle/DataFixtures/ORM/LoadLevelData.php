<?php
namespace Ens\JobeetBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ens\JobeetBundle\Entity\Level;

class LoadLevelData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $junior = new Level();
        $junior->setNom('Junior');

        $medior = new Level();
        $medior->setNom('Medior');

        $senior = new Level();
        $senior->setNom('Senior');


        $em->persist($junior);
        $em->persist($medior);
        $em->persist($senior);

        $em->flush();

        $this->addReference('level-junior', $junior); 
        $this->addReference('level-medior', $medior); 
        $this->addReference('level-senior', $senior);
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}