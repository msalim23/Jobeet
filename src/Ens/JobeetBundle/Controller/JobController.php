<?php

namespace Ens\JobeetBundle\Controller;

use Ens\JobeetBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Job controller.
 *
 * @Route("/job")
 */
class JobController extends Controller {
	/**
	 * Lists all job entities.
	 *
	 * @Route("/", name="ens_job")
	 * 
	 * @method ("GET")
	 */
	public function indexAction(Request $request) {
		$em = $this->getDoctrine ()->getManager ();
		
		$categories = $em->getRepository ( 'EnsJobeetBundle:Category' )->getWithJobs ();
		
		foreach ( $categories as $category ) {
			$category->setActiveJobs ( $em->getRepository ( 'EnsJobeetBundle:Job' )->getActiveJobs ( $category->getId (), $this->container->getParameter ( 'max_jobs_on_homepage' ) ) );
			
			$category->setMoreJobs($em->getRepository('EnsJobeetBundle:Job')->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage'));
		}
		$format = $request->getRequestFormat();
		
		return $this->render('EnsJobeetBundle:Job:index.'.$format.'.twig', array(
				'categories' => $categories,
				'lastUpdated' => $em->getRepository('EnsJobeetBundle:Job')->getLatestPost()->getCreatedAt()->format(DATE_ATOM),
				'feedId' => sha1($this->get('router')->generate('ens_job', array('_format'=> 'atom'), true)),
		));
	}
	
	/**
	 * Creates a new job entity.
	 *
	 * @Route("/new", name="ens_job_new")
	 * 
	 * @method ({"GET", "POST"})
	 */
	public function newAction(Request $request) {
		$job = new Job ();
		$job->setType('full-time');
		$form = $this->createForm ( 'Ens\JobeetBundle\Form\JobType', $job );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			$em = $this->getDoctrine ()->getManager ();
			$em->persist ( $job );
			$em->flush ( $job );
			
			return $this->redirect($this->generateUrl('ens_job_preview', array(
					'company' => $job->getCompanySlug(),
					'location' => $job->getLocationSlug(),
					'token' => $job->getToken(),
					'position' => $job->getPositionSlug()
			)));
		}
		
		return $this->render ( 'EnsJobeetBundle:Job:new.html.twig', array (
				'job' => $job,
				'form' => $form->createView () 
		) );
	}
	
	/**
	 * Finds and displays a job entity.
	 *
	 * @Route("/{company}/{location}/{id}/{position}", name="ens_job_show", requirements={"id": "\d+"})
	 * 
	 * @method ("GET")
	 */
	public function showAction(Request $request ,$id) {
		$em = $this->getDoctrine ()->getManager ();
		$entity= $em->getRepository ( 'EnsJobeetBundle:Job' )->getActiveJob ( $id );
	
		
		if (! $entity) {
			throw $this->createNotFoundException ( 'No job found for id ' . $id );
		}
		
		$session = $request->getSession();
		
		// fetch jobs already stored in the job history
		$jobs = $session->get('job_history', array());
		
		// store the job as an array so we can put it in the session and avoid entity serialize errors
		$job = array('id' => $entity->getId(),
			'level' =>$entity->getLevelId(),
			'position' =>$entity->getPosition(),
			'company' => $entity->getCompany(), 'companyslug' => $entity->getCompanySlug(), 'locationslug' => $entity->getLocationSlug(), 'positionslug' => $entity->getPositionSlug());
		
		if (!in_array($job, $jobs)) {
			// add the current job at the beginning of the array
			array_unshift($jobs, $job);
			
			// store the new job history back into the session
			$session->set('job_history', array_slice($jobs, 0, 3));
		}
		
		$deleteForm = $this->createDeleteForm($entity);
		
		return $this->render('EnsJobeetBundle:Job:show.html.twig', array(
				'entity'      => $entity,
				'delete_form' => $deleteForm->createView(),
		));
	}
	
	/**
	 * Displays a form to edit an existing job entity.
	 *
	 * @Route("/{token}/edit", name="ens_job_edit")
	 * 
	 * @method ({"GET", "POST"})
	 */
	public function editAction(Request $request, Job $job) {
		$deleteForm = $this->createDeleteForm ( $job );
		$editForm = $this->createForm ( 'Ens\JobeetBundle\Form\JobType', $job );
		$editForm->handleRequest ( $request );
		
		if ($editForm->isSubmitted () && $editForm->isValid ()) {
			$this->getDoctrine ()->getManager ()->flush ();
			
			return $this->redirect($this->generateUrl('ens_job_preview', array(
					'company' => $job->getCompanySlug(),
					'location' => $job->getLocationSlug(),
					'token' => $job->getToken(),
					'position' => $job->getPositionSlug()
			)));
		}
		
		return $this->render ( 'EnsJobeetBundle:Job:edit.html.twig', array (
				'job' => $job,
				'edit_form' => $editForm->createView (),
				'delete_form' => $deleteForm->createView () 
		) );
	}
	
	/**
	 * Deletes a job entity.
	 *
	 * @Route("/{token}", name="ens_job_delete")
	 * 
	 * @method ("GET")
	 */
	public function deleteAction(Request $request, Job $job) {
		$form = $this->createDeleteForm ( $job );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			$em = $this->getDoctrine ()->getManager ();
			$em->remove ( $job );
			$em->flush ( $job );
		}
		
		return $this->redirectToRoute ( 'ens_job' );
	}
	
	/**
	 * Creates a form to delete a job entity.
	 *
	 * @param Job $job
	 *        	The job entity
	 *        	
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Job $job) {
		return $this->createFormBuilder ()->setAction ( $this->generateUrl ( 'ens_job_delete', array (
				'token' => $job->getToken() 
		) ) )->setMethod ( 'GET' )->getForm ();
	}
	/**
	 * displays a job preview entity.
	 *
	 * @Route("/{company}/{location}/{token}/{position}", name="ens_job_preview")
	 *
	 * 
	 */
	public function previewAction($token)
	{
		$em = $this->getDoctrine()->getManager();
		
		$job = $em->getRepository('EnsJobeetBundle:Job')->findOneByToken($token);
		
		if (!$job) {
			throw $this->createNotFoundException('Unable to find Job entity.');
		}
		
		$deleteForm = $this->createDeleteForm($job);
		$publishForm = $this->createPublishForm($job->getToken());
		$extendForm = $this->createExtendForm($job->getToken());
		
		return $this->render('EnsJobeetBundle:Job:show.html.twig', array(
				'job'      => $job,
				'delete_form' => $deleteForm->createView(),
				'publish_form' => $publishForm->createView(),
				'extend_form' => $extendForm->createView(),
		));
	}
	
	/**
	 * Publishes job entity that is not activated.
	 *
	 * @Route("/{token}/publish", name="ens_job_publish")
	 *
	 * @method ({"POST"})
	 */
	public function publishAction(Request $request, $token)
	{
		$form = $this->createPublishForm($token);
		
		
		$form->handleRequest($request);
		
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('EnsJobeetBundle:Job')->findOneByToken($token);
			
			if (!$entity) {
				throw $this->createNotFoundException('Unable to find Job entity.');
			}
			
			$entity->publish();
			$em->persist($entity);
			$em->flush();
			
			
			$this->get('session')->getFlashBag()->add('notice','Your job is now online for 30 days.');
		}
        
        $logger = $this->get('logger');
        $logger->info('Un job a été créé');
        
		return $this->redirect($this->generateUrl('ens_job_preview', array(
				'company' => $entity->getCompanySlug(),
				'location' => $entity->getLocationSlug(),
				'token' => $entity->getToken(),
				'position' => $entity->getPositionSlug()
		)));
	}
	
	private function createPublishForm($token)
	{
		return $this->createFormBuilder(array('token' => $token))
		->add('token', HiddenType::class)
		->getForm()
		;
	}
	
	/**
	 * Extends job entity that is almost not valid anymore.
	 *
	 * @Route("/{token}/extend", name="ens_job_extend")
	 *
	 * @method ({"POST"})
	 */
	public function extendAction(Request $request, $token)
	{
		$form = $this->createExtendForm($token);
		$form->handleRequest($request);
		
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('EnsJobeetBundle:Job')->findOneByToken($token);
			
			
			if (!$entity) {
				throw $this->createNotFoundException('Unable to find Job entity.');
			}
			
			if (!$entity->extend()) {
				throw $this->createNotFoundException('Unable to find extend the Job.');
			}
			
			$expiresAt =new \DateTime(date('Y-m-d H:i:s', time()+ 86400 * 30));
			$entity->setExpiresAt($expiresAt);
			$em->persist($entity);
			$em->flush();
			
			
			$this->get('session')->getFlashBag()->set('notice', sprintf('Your job validity has been extended until %s.', $entity->getExpiresAt()->format('m/d/Y')));
		}
		
		return $this->redirect($this->generateUrl('ens_job_preview', array(
				'company' => $entity->getCompanySlug(),
				'location' => $entity->getLocationSlug(),
				'token' => $entity->getToken(),
				'position' => $entity->getPositionSlug()
		)));
	}
	
	private function createExtendForm($token)
	{
		return $this->createFormBuilder(array('token' => $token))
		->add('token', HiddenType::class)
		->getForm()
		;
	}
}
