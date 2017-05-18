<?php

namespace Ens\JobeetBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;

class JobAdmin extends AbstractAdmin
{
	// setup the defaut sort column and order
	protected $datagridValues = array(
			'_sort_order' => 'DESC',
			'_sort_by' => 'created_at'
	);
	
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
		->add('categoryId')
		->add('type', 'choice', array('choices' => Job::getTypes(), 'expanded' => true))
		->add('company')
		->add('file', 'file', array('label' => 'Company logo', 'required' => false))
		->add('url')
		->add('position')
		->add('location')
		->add('description')
		->add('howToApply')
		->add('isPublic')
		->add('email')
		->add('isActivated')
		;
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
		->add('categoryId')
		->add('company')
		->add('position')
		->add('description')
		->add('isActivated')
		->add('isPublic')
		->add('email')
		->add('expiresAt')
		;
	}
	
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
		->addIdentifier('company')
		->add('position')
		->add('location')
		->add('url')
		->add('isActivated')
		->add('email')
		->add('categoryId')
		->add('expiresAt')
		->add('_action', 'actions', array(
				'actions' => array(
						'view' => array(),
						'edit' => array(),
						'delete' => array(),
				)
		))
		;
	}
	
	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
		->add('categoryId')
		->add('type')
		->add('company')
		->add('webPath', 'string', array('template' => 'EnsJobeetBundle:JobAdmin:list_image.html.twig'))
		->add('url')
		->add('position')
		->add('location')
		->add('description')
		->add('howToApply')
		->add('isPublic')
		->add('isActivated')
		->add('token')
		->add('email')
		->add('expiresAt')
		;
	}
	
	public function getBatchActions()
	{
		// retrieve the default (currently only the delete action) actions
		$actions = parent::getBatchActions();
		
		// check user permissions
		if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')) {
			$actions['extend'] = array(
					'label'            => 'Extend',
					'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
			);
			
			$actions['deleteNeverActivated'] = array(
					'label'            => 'Delete never activated jobs',
					'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
			);
		}
		
		return $actions;
	}
}