<?php

namespace Ens\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/nothing")
     */
    public function indexAction()
    {
        return $this->render('EnsJobeetBundle:Default:index.html.twig');
    }
    
    /**
     * @Route(" /login_check", name="login_check")
     */
    public function loginAction(Request $request)
    {
    	
    }
    
    /**
     * @Route(" /login", name="login")
     */
    public function loginCheckAction(Request $request)
    {
    	
    	$session = $request->getSession();
    	
    	// get the login error if there is one
    	if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
    		$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
    	} else {
    		$error = $session->get(Security::AUTHENTICATION_ERROR);
    		$session->remove(Security::AUTHENTICATION_ERROR);
    	}
    	
    	return $this->render('EnsJobeetBundle:Default:login.html.twig', array(
    			// last username entered by the user
    			'last_username' => $session->get(Security::LAST_USERNAME),
    			'error'         => $error,
    	));
    }
    
    /**
     * @Route(" /logout", name="logout")
     */
    public function logOutAction(Request $request)
    {
    	
    }
}
