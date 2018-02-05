<?php

namespace docono\CommunicationBundle\Controller;

use docono\CommunicationBundle\Form\BusinessContactFormType;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Enquiry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormController extends FrontendController
{
	protected $layout = 'layout.html.php';

	public function contactAction(Request $request) {
		$formService = $this->container->get('docono.communication.form');

		$successful = $formService->setForm(BusinessContactFormType::class)->setDataObject(new Enquiry())->process();

		$form = $formService->getForm();

		//add the form view
		$this->view->layout = $this->layout;
		$this->view->form = $form->createView();
		$this->view->submitted = $form->isSubmitted();
		$this->view->successful = $successful;
	}
}
