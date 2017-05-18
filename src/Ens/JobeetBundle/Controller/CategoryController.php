<?php

namespace Ens\JobeetBundle\Controller;

use Ens\JobeetBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 * @Route("ens_category")
 */
class CategoryController extends Controller {
	/**
	 * Finds and displays a category entity.
	 *
	 * @Route("/{slug}/{page}", name="ens_category_show", defaults={"page": 1})
	 * 
	 * @method ("GET")
	 */
	public function showAction($slug, $page, Request $request) {
		$em = $this->getDoctrine()->getManager();
		
		$category = $em->getRepository('EnsJobeetBundle:Category')->findOneBySlug($slug);
		
		if (!$category) {
			throw $this->createNotFoundException('Unable to find Category entity.');
		}
		
		$total_jobs = $em->getRepository('EnsJobeetBundle:Job')->countActiveJobs($category->getId());
		$jobs_per_page = $this->container->getParameter('max_jobs_on_category');
		$last_page = ceil($total_jobs / $jobs_per_page);
		$previous_page = $page > 1 ? $page - 1 : 1;
		$next_page = $page < $last_page ? $page + 1 : $last_page;
		
		$category->setActiveJobs($em->getRepository('EnsJobeetBundle:Job')->getActiveJobs($category->getId(), $jobs_per_page, ($page - 1) * $jobs_per_page));
		$format = $request->getRequestFormat();
		return $this->render('EnsJobeetBundle:Category:show.'.$format.'.twig', array(
				'category' => $category,
				'last_page' => $last_page,
				'previous_page' => $previous_page,
				'current_page' => $page,
				'next_page' => $next_page,
				'total_jobs' => $total_jobs,
				'feedId' => sha1($this->get('router')->generate('ens_category_show', array('slug' =>  $category->getSlug(), '_format' => 'atom'), true)),
		));
	}
}
