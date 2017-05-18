<?php

namespace Ens\JobeetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase {
	
// 	public function testIndex()
// 	{
// 		$client = static::createClient();
		
// 		$crawler = $client->request('GET', '/');
// 		$this->assertEquals('Ens\JobeetBundle\Controller\JobController::indexAction', $client->getRequest()->attributes->get('_controller'));
// 		$this->assertTrue($crawler->filter('.jobs td.position:contains("Expired")')->count() == 0);
		
// 		$kernel = static::createKernel();
// 		$kernel->boot();
// 		$max_jobs_on_homepage = $kernel->getContainer()->getParameter('max_jobs_on_homepage');
// 		$this->assertTrue($crawler->filter('.category_programming tr')->count() == $max_jobs_on_homepage);
// 		$this->assertTrue($crawler->filter('.category_design .more_jobs')->count() == 0);
// 		$this->assertTrue($crawler->filter('.category_programming .more_jobs')->count() == 1);
		
// 		$this->assertTrue($crawler->filter('.category_programming tr')->first()->filter(sprintf('a[href*="/%d/"]', $this->getMostRecentProgrammingJob()->getId()))->count() == 1);
		
// 		$job = $this->getMostRecentProgrammingJob();
// 		$link = $crawler->selectLink('Web Developer')->first()->link();
// 		$crawler = $client->click($link);
// 		$this->assertEquals('Ens\JobeetBundle\Controller\JobController::showAction', $client->getRequest()->attributes->get('_controller'));
// 		$this->assertEquals($job->getCompanySlug(), $client->getRequest()->attributes->get('company'));
// 		$this->assertEquals($job->getLocationSlug(), $client->getRequest()->attributes->get('location'));
// 		$this->assertEquals($job->getPositionSlug(), $client->getRequest()->attributes->get('position'));
// 		$this->assertEquals($job->getId(), $client->getRequest()->attributes->get('id'));
// 	}
	
	
	public function getMostRecentProgrammingJob()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		
		$query = $em->createQuery('SELECT j from EnsJobeetBundle:Job j LEFT JOIN j.categoryId c WHERE c.slug = :slug AND j.expiresAt > :date ORDER BY j.createdAt DESC');
		$query->setParameter('slug', 'programming');
		$query->setParameter('date', date('Y-m-d H:i:s', time()));
		$query->setMaxResults(1);
		return $query->getSingleResult();
	}
	
	public function testJobForm()
	{
		$client = static::createClient();
		
		$crawler = $client->request('GET', '/job/new');
		$this->assertEquals('Ens\JobeetBundle\Controller\JobController::newAction', $client->getRequest()->attributes->get('_controller'));
		
		$form = $crawler->selectButton('Preview your job')->form(array(
				'ens_jobeetbundle_job[company]'      => 'Sensio Labs',
				'ens_jobeetbundle_job[type]'		 => 'part-time',
				'ens_jobeetbundle_job[url]'          => 'http://www.sensio.com/',
				'ens_jobeetbundle_job[file]'         => __DIR__.'/../../../../../web/bundles/ensjobeet/images/sensio-labs.gif',
				'ens_jobeetbundle_job[position]'     => 'Developer',
				'ens_jobeetbundle_job[location]'     => 'Atlanta, USA',
				'ens_jobeetbundle_job[description]'  => 'You will work with symfony to develop websites for our customers.',
				'ens_jobeetbundle_job[howToApply]' => 'Send me an email',
				'ens_jobeetbundle_job[email]'        => 'for.a.job@example.com',
				'ens_jobeetbundle_job[isPublic]'    => true,
		));
		
		$client->submit($form);
		$this->assertEquals('Ens\JobeetBundle\Controller\JobController::newAction', $client->getRequest()->attributes->get('_controller'));
		
		$client->followRedirect();
		$this->assertEquals('Ens\JobeetBundle\Controller\JobController::previewAction', $client->getRequest()->attributes->get('_controller'));
		
		$kernel = static::createKernel();
		$kernel->boot();
		$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		
		$query = $em->createQuery('SELECT count(j.id) from EnsJobeetBundle:Job j WHERE j.location = :location AND j.isActivated =0 AND j.isPublic = 0');
		$query->setParameter('location', 'Atlanta, USA');
		$this->assertTrue(0 < $query->getSingleScalarResult());
		
// 		$crawler = $client->request('GET', '/job/new');
// 		$form = $crawler->selectButton('Preview your job')->form(array(
// 				'ens_jobeetbundle_job[company]'      => 'Sensio Labs',
// 				'ens_jobeetbundle_job[position]'     => 'Developer',
// 				'ens_jobeetbundle_job[location]'     => 'Atlanta, USA',
// 				'ens_jobeetbundle_job[email]'        => 'not.an.email',
// 		));
// 		$crawler = $client->submit($form);
		
// 		// check if we have 3 errors
// 		$this->assertTrue($crawler->filter('.error_list')->count() == 0);
// 		// check if we have error on job_description field
// 		$this->assertTrue($crawler->filter('#job_description')->siblings()->first()->filter('.error_list')->count() == 1);
// 		// check if we have error on job_how_to_apply field
// 		$this->assertTrue($crawler->filter('#job_how_to_apply')->siblings()->first()->filter('.error_list')->count() == 1);
// 		// check if we have error on job_email field
// 		$this->assertTrue($crawler->filter('#job_email')->siblings()->first()->filter('.error_list')->count() == 1);
		
	}
	
	public function createJob($values = array(), $publish = false)
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/job/new');
		$form = $crawler->selectButton('Preview your job')->form(array_merge(array(
				'ens_jobeetbundle_job[company]'      => 'Sensio Labs',
				'ens_jobeetbundle_job[url]'          => 'http://www.sensio.com/',
				'ens_jobeetbundle_job[position]'     => 'Developer',
				'ens_jobeetbundle_job[location]'     => 'Atlanta, USA',
				'ens_jobeetbundle_job[description]'  => 'You will work with symfony to develop websites for our customers.',
				'ens_jobeetbundle_job[howToApply]' => 'Send me an email',
				'ens_jobeetbundle_job[email]'        => 'for.a.job@example.com',
				'ens_jobeetbundle_job[isPublic]'    => false,
		), $values));
		
		$client->submit($form);
		$client->followRedirect();
		
		if($publish) {
			$crawler = $client->getCrawler();
			$form = $crawler->selectButton('Publish')->form();
			$client->submit($form);
			$client->followRedirect();
		}
		
		
		return $client;
	}
	
	public function testPublishJob()
	{
		$client = $this->createJob(array('ens_jobeetbundle_job[position]' => 'FOO1'));
		$crawler = $client->getCrawler();
		$form = $crawler->selectButton('Publish')->form();
		$client->submit($form);
		
		$kernel = static::createKernel();
		$kernel->boot();
		$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		
		$query = $em->createQuery('SELECT count(j.id) from EnsJobeetBundle:Job j WHERE j.position = :position AND j.isActivated = 1');
		$query->setParameter('position', 'FOO1');
		$this->assertTrue(0 < $query->getSingleScalarResult());
	}
	
	public function testDeleteJob()
	{
		$client = $this->createJob(array('ens_jobeetbundle_job[position]' => 'FOO2'));
		$crawler = $client->getCrawler();
		$form = $crawler->selectButton('Delete')->form();
		$client->submit($form);
		
		$kernel = static::createKernel();
		$kernel->boot();
		$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		
		$query = $em->createQuery('SELECT count(j.id) from EnsJobeetBundle:Job j WHERE j.position = :position');
		$query->setParameter('position', 'FOO2');
		$this->assertTrue(0 == $query->getSingleScalarResult());
	}
	
	public function getJobByPosition($position)
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		
		$query = $em->createQuery('SELECT j from EnsJobeetBundle:Job j WHERE j.position = :position');
		$query->setParameter('position', $position);
		$query->setMaxResults(1);
		return $query->getSingleResult();
	}
	
	public function editAction($token)
	{
		$em = $this->getDoctrine()->getEntityManager();
		
		$entity = $em->getRepository('EnsJobeetBundle:Job')->findOneByToken($token);
		
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Job entity.');
		}
		
		if ($entity->getIsActivated()) {
			throw $this->createNotFoundException('Job is activated and cannot be edited.');
		}
	}
	
	public function testExtendJob()
	{
		// A job validity cannot be extended before the job expires soon
		$client = $this->createJob(array('ens_jobeetbundle_job[position]' => 'FOO4'), true);
		$crawler = $client->getCrawler();
		$this->assertTrue($crawler->filter('input[type=submit]:contains("Extend")')->count() == 0);
		
		// A job validity can be extended when the job expires soon
		
		// Create a new FOO5 job
		$client = $this->createJob(array('ens_jobeetbundle_job[position]' => 'FOO5'), true);
		// Get the job and change the expire date to today
		$kernel = static::createKernel();
		$kernel->boot();
		$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
		$job = $em->getRepository('EnsJobeetBundle:Job')->findOneByPosition('FOO5');
		$job->setExpiresAt(new \DateTime());
		$em->flush();
		// Go to the preview page and extend the job
		$crawler = $client->request('GET', sprintf('/job/%s/%s/%s/%s', $job->getCompanySlug(), $job->getLocationSlug(), $job->getToken(), $job->getPositionSlug()));
		$crawler = $client->getCrawler();
		$form = $crawler->selectButton('Extend')->form();
		$client->submit($form);
		// Reload the job from db
		$job = $this->getJobByPosition('FOO5');
		// Check the expiration date
		$this->assertTrue($job->getExpiresAt()->format('y/m/d') == date('y/m/d', time() + 86400 * 30));
	}
}
