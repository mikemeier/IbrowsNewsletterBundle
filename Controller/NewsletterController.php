<?php

namespace Ibrows\Bundle\NewsletterBundle\Controller;

use Ibrows\Bundle\NewsletterBundle\Form\NewsletterFormType;

use Ibrows\Bundle\NewsletterBundle\Annotation\Wizard\Annotation as WizardAction;
use Ibrows\Bundle\NewsletterBundle\Annotation\Wizard\AnnotationHandler as WizardActionHandler;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/newsletter")
 */
class NewsletterController extends AbstractController
{
	/**
	 * @Route("/", name="ibrows_newsletter_index")
	 */
	public function indexAction()
	{
        $this->setNewsletter(null);
        
        return $this->render($this->getTemplateManager()->getNewsletter('index'), array(
            
		));
	}
    
    /**
	 * @Route("/list", name="ibrows_newsletter_list")
	 */
	public function listAction()
	{
        $this->setNewsletter(null);
        
		return $this->render($this->getTemplateManager()->getNewsletter('list'), array(
            'newsletters' => $this->getMandant()->getNewsletters()
		));
	}
    
    /**
	 * @Route("/edit/redirection/{id}", name="ibrows_newsletter_edit_redirection")
	 */
	public function editredirectionAction($id)
	{
        $newsletter = $this->getNewsletterById($id);
        $this->setNewsletter($newsletter);
        
        return $this->redirect($this->generateUrl('ibrows_newsletter_edit'));
	}
	
	/**
	 * @Route("/create", name="ibrows_newsletter_create")
     * @WizardAction(name="create", number=1, validationMethod="createValidation")
	 */
	public function createAction()
	{
        $this->setNewsletter(null);
        
		$newsletter = $this->getNewsletterManager()->create();
		
		$formtype = $this->getClassManager()->getForm('newsletter');
		$form = $this->createForm(new $formtype(), $newsletter);
		
		$request = $this->getRequest();
		if($request->getMethod() == 'POST'){
			$form->bindRequest($request);
			
			if($form->isValid()){
                $this->setNewsletter($newsletter);
				return $this->redirect($this->getWizardActionAnnotationHandler()->getNextStepUrl());
			}
		}
	
		return $this->render($this->getTemplateManager()->getNewsletter('create'), array(
            'newsletter' => $newsletter,
            'form' => $form->createView(),
            'wizard' => $this->getWizardActionAnnotationHandler(),
		));
	}
	
    public function createValidation(WizardActionHandler $handler)
    {
        
    }
    
	/**
	 * @Route("/edit", name="ibrows_newsletter_edit")
     * @WizardAction(name="edit", number=2, validationMethod="editValidation")
	 */
	public function editAction()
	{
        if(($response = $this->getWizardActionValidation()) instanceof Response){
            return $response;
        }

        $request = $this->getRequest();
		if($request->getMethod() == 'POST'){
			return $this->redirect($this->getWizardActionAnnotationHandler()->getNextStepUrl());
		}
        
		return $this->render($this->getTemplateManager()->getNewsletter('edit'), array(
            'blockProviders' => $this->getBlockProviderManager()->getBlockProviders(),
            'newsletter' => $this->getNewsletter(),
            'wizard' => $this->getWizardActionAnnotationHandler(),
		));
	}
    
    public function editValidation(WizardActionHandler $handler)
    {
        if(is_null($this->getNewsletter())){
            return $this->redirect($handler->getStepUrl($handler->getLastValidAnnotation()));
        }
    }
    
    /**
	 * @Route("/subscriber", name="ibrows_newsletter_subscriber")
     * @WizardAction(name="subscriber", number=3, validationMethod="subscriberValidation")
	 */
	public function subscriberAction()
	{
        if(($response = $this->getWizardActionValidation()) instanceof Response){
            return $response;
        }
        
        $newsletter = $this->getNewsletter();
        $formtype = $this->getClassManager()->getForm('subscriber');
        $form = $this->createForm(new $formtype(), $newsletter);
        
        $request = $this->getRequest();
        if($request->getMethod() == 'POST'){
	        	$form->bindRequest($request);
	        		
	        	if($form->isValid()){
	        		$this->setNewsletter($newsletter);
	        		return $this->redirect($this->getWizardActionAnnotationHandler()->getNextStepUrl());
	        	}
        }
        
		return $this->render($this->getTemplateManager()->getNewsletter('subscriber'), array(
            'newsletter' => $this->getNewsletter(),
			'form' => $form->createView(),
            'wizard' => $this->getWizardActionAnnotationHandler(),
		));
	}
    
    public function subscriberValidation(WizardActionHandler $handler)
    {
        if(is_null($this->getNewsletter())){
            return $this->redirect($handler->getStepUrl($handler->getLastValidAnnotation()));
        }
    }
    
    /**
	 * @Route("/settings", name="ibrows_newsletter_settings")
     * @WizardAction(name="settings", number=4, validationMethod="settingsValidation")
	 */
	public function settingsAction()
	{
        if(($response = $this->getWizardActionValidation()) instanceof Response){
            return $response;
        }
        
		return $this->render($this->getTemplateManager()->getNewsletter('settings'), array(
            'newsletter' => $this->getNewsletter(),
            'wizard' => $this->getWizardActionAnnotationHandler(),
		));
	}
    
    public function settingsValidation(WizardActionHandler $handler)
    {
        $newsletter = $this->getNewsletter();
        
        if(is_null($newsletter)){
            return $this->redirect($handler->getStepUrl($handler->getLastValidAnnotation()));
        }
        
        if(count($newsletter->getSubscribers()) <= 0){
            return $this->redirect($this->generateUrl('ibrows_newsletter_subscriber'));
        }
    }
	
	/**
	 * @Route("/summary", name="ibrows_newsletter_summary")
     * @WizardAction(name="summary", number=5, validationMethod="summaryValidation")
	 */
	public function summaryAction()
	{
        if(($response = $this->getWizardActionValidation()) instanceof Response){
            return $response;
        }
        
		return $this->render($this->getTemplateManager()->getNewsletter('summary'), array(
            'newsletter' => $this->getNewsletter(),
            'wizard' => $this->getWizardActionAnnotationHandler(),
		));
	}
    
    public function summaryValidation(WizardActionHandler $handler)
    {
        $newsletter = $this->getNewsletter();
        
        if(is_null($newsletter)){
            return $this->redirect($handler->getStepUrl($handler->getLastValidAnnotation()));
        }
        
        if(count($newsletter->getSubscribers()) <= 0){
            return $this->redirect($this->generateUrl('ibrows_newsletter_subscriber'));
        }
    }
    
    /**
	 * @Route("/send", name="ibrows_newsletter_send")
	 */
	public function sendAction()
	{
        $newsletter = $this->getNewsletter();
        if(is_null($newsletter)){
            return $this->redirect($this->generateUrl('ibrows_newsletter_index', array(), true));
        }
        
		return $this->render($this->getTemplateManager()->getNewsletter('send'), array(
            'newsletter' => $newsletter
		));
	}
}
