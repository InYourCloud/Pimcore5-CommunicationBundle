<?php
/**
 * DOCONO | digitale Problemlöser
 *
 * @author Renzo Müller <renzo@docono.io>
 * @copyright  Copyright (c) DOCONO  (https://docono.io)
 * @since 1.0.0
 */

namespace CommunicationBundle\Services;

use Pimcore\Log\Simple;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Mail;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class Form
{
	/**
	 * @var TranslatorInterface
	 */
	private $translator = null;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $request = null;

	/**
	 * @var FormFactory
	 */
	private $formFactory = null;

	/**
	 * @var \Symfony\Component\Form\AbstractType
	 */
	private $form = null;

	/**
	 * @var string
	 */
	private $formType = null;

	/**
	 * @var \Pimcore\Model\DataObject\Concrete
	 */
	private $dataObject = null;

	/**
	 * @var string
	 */
	private $objDir = null;

	/**
	 * @var array
	 */
	private $contentTable = null;

	/**
	 * Form constructor
	 *
	 * @param TranslatorInterface $translator
	 * @param RequestStack $request_stack
	 * @param FormFactory $formFactory
	 */
	public function __construct(TranslatorInterface $translator, RequestStack $request_stack, FormFactory $formFactory) {
		$this->translator = $translator;
		$this->request = $request_stack->getCurrentRequest();
		$this->formFactory = $formFactory;
	}

	/**
	 * @param string $type
	 * @return Form
	 */
	public function setForm(string $type) : Form {
		$this->formType = $type;
		$this->form = $this->formFactory->create($type);

		return $this;
	}

	/**
	 * @param DataObject\Concrete $dataObject
	 * @param string $dir
	 * @return Form
	 */
	public function setDataObject(DataObject\Concrete $dataObject, string $dir='/') : Form {
		$this->dataObject = $dataObject;
		$this->objDir = $dir;

		return $this;
	}

	/**
	 * @return \Symfony\Component\Form\Form
	 * @throws \Exception
	 */
	public function getForm() : \Symfony\Component\Form\Form
	{
		if(!$this->form)
			throw new \Exception('no form defined');

		return $this->form;
	}

	/**
	 * processing form
	 * - is form submitted
	 * - is form valid
	 * - handle dataObject
	 * - send notification
	 * - send confirmation
	 * - reset form
	 *
	 * @return bool
	 */
	public function process() : bool {
		$success = false;

		try {
			$form = $this->getForm();

			$form->handleRequest($this->request);
		} catch(\Exception $e) {
			Simple::log('docono_communication', 'processing form failed: ' . $e->getMessage());

			return false;
		}

		if($form->isSubmitted()) {
			if($form->isValid()) {
				//data object handling
				if($this->dataObject) {
					$this->handleObject();
				}

				//notification email handling
				if(!$this->sendNotification())
					return false;

				//confirmation email handling
				$this->sendConfirmation();

				$success = true;

				//clear form
//					unset($this->form);
				//reinit form
//					$this->form = $this->formFactory->create($this->formType);
			}
		}

		return $success;
	}

	/**
	 * sending the notification email to the admin
	 * - the set email in the template will be used as the recipient
	 *
	 * @return bool
	 */
	public function sendNotification(): bool
	{
		$mailDocument = Document::getByPath($this->request->getPathInfo().'/notification');

		if(!$mailDocument)
			return false;

		try {
			$formData = $this->form->getData();

			//init email
			$mail = new Mail();
			//set mail document
			$mail->setDocument($mailDocument);
			//set mail data
			$mail->setParams($formData);
			$mail->setParam('contentTable', $this->getContentTable());

			//send mail
			$mail->send();
		} catch(\Exception $e) {
			Simple::log('docono_communication', 'sending notification failed: ' . $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * sending the confirmation mail to the customer if template is set
	 * - field 'email' will be used as the recipient
	 *
	 * @return bool
	 */
	public function sendConfirmation(): bool
	{
		$mailDocument = Document::getByPath($this->request->getPathInfo().'/confirmation');

		if(!$mailDocument)
			return false;

		try {
			$formData = $this->form->getData();

			//init email
			$mail = new Mail();
			//set mail document
			$mail->setDocument($mailDocument);
			//set mail data
			$mail->setParams($formData);
			//add recipient
			$mail->addTo($formData['email'][1]);

			//send mail
			$mail->send();
		} catch(\Exception $e) {
			Simple::log('docono_communication', 'sending confirmation failed: ' . $e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * fills in the form fields into the given dataObject and saves it to the given directory
	 *
	 * @return bool
	 */
	protected function handleObject() : bool {
		//get object reference
		$obj =& $this->dataObject;

		//get form data
		$formData = $this->form->getData();

		try {
			foreach($formData as $key => $value) {
				$setter = 'set' . ucfirst($key);

				//ensure field exists
				if(!method_exists($obj, $setter)) continue;

				//assign value to property
				call_user_func([$obj, $setter], $value);
			}

			$obj->setPublished(true);
			//set parent folder
			$obj->setParent(DataObject\Folder::getByPath($this->objDir));

			//@todo configurable object key
			$obj->setKey(time());

			$obj->save();
		} catch(\Exception $e) {
			Simple::log('docono_communication', $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * wrap form data into a html table
	 *
	 * @return string
	 */
	public function getContentTable() {
		if(!$this->contentTable) {
			$formData = $this->form->getData();

			$contentTable = '<table>';

			foreach($formData as $key => $value) {
				//get field config
				$config = $this->form->get($key)->getConfig();

				//generate row
				$contentTable .= '<tr><td>' . $this->translator->trans($config->getOption('label')) . '</td><td>' . trim($value) . '</td></tr>';
			}

			$this->contentTable = $contentTable . '</table>';
		}

		return $this->contentTable;
	}
}