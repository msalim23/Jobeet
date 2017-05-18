<?php

namespace Ens\JobeetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ens\JobeetBundle\Entity\Job;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder->add('categoryId')
        		->add('type', ChoiceType::class, array('choices' => Job::getTypes(), 'expanded' => true))
                ->add('levelId', ChoiceType::class, array('choices' => Job::getLevels(), 'expanded' => false))
        		->add('company')
        		->add('file', FileType::class, array('label' => 'Company logo', 'required' => false))
		        ->add('url')
		        ->add('position')
		        ->add('location')
		        ->add('description')
		        ->add('howToApply', null, array('label' => 'How to apply?'))
		        ->add('isPublic', null, array('label' => 'Public?'))
		        ->add('isActivated')
		        ->add('email');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ens\JobeetBundle\Entity\Job'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ens_jobeetbundle_job';
    }
    
    


}
