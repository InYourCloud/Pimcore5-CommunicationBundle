# DOCONO.io Communication Bundle
* [Description](#description)
* [Getting Started](#getting-started)
* [Form Service](#form-service)
* [Slack Service](#slack-service)

<a name="description"/>

## Description
DOCONOs Communication Bundle provides services to simplify the form handling and submit messages to Slack.

<a name="getting-started"/>

## Getting Started
* download bundle with Composer ```"docono/communication-bundle": "^1.0"```
* install the Bundle in the Extension Management Tool in Pimcore

<a name="form-service"/>

## Form Service

### methods
| method                                                                    | description                                      |
|---------------------------------------------------------------------------|--------------------------------------------------|
| setForm(string $type) : Form                                              | set form type                                    |
| setDataObject(DataObject\Concrete $dataObject, string $dir='/') : Form    | set dataObject                                   |
| getForm() : \Symfony\Component\Form\AbstractType                          | get form                                         |
| process() : bool                                                          | process the form submission and email handling   |
| function sendNotification(): bool                                         | send notification email                          |
| sendConfirmation(): bool                                                  | send confirmation email                          |
| getContentTable()                                                         | get html table with the form values              |


### form types
| Name                    | class                    | fields                                                                     |
|-------------------------|--------------------------|----------------------------------------------------------------------------|
| simple contact form     | SimpleContactFormType    | name, email, message                                                       |
| standard contact form   | ContactFormType          | firstname, lastname, address, town, email, phone, message                  |
| business contact form   | BusinessContactFormType  | title, firstname, lastname, company, address, town, email, phone, message  |

### email handling
The services requires at least an emailDocument called 'notification' as direct child document of the form. This email will be submitted to the email address given in the template.
To send also an email to the customer, simply creat another emailDocument as direct child document called 'confirmation'. The form field 'email' will be used as as customer email.

### dataObject handling
To use dataObject, you need to set an instance with `setDataObject($dataObject)`.
Be aware that the object members must be named as the fields in the form.

### basic usage
```php
$formService = $this->container->get('docono.communication.form');

$successful = $formService->setForm(BusinessContactFormType::class)->setDataObject(new Enquiry())->process();

$form = $formService->getForm();

$this->view->form = $form->createView();
$this->view->submitted = $form->isSubmitted();
$this->view->successful = $successful;
```

### controller and view
The bundle comes with a basic MailController and FormController.
If you use Zurbs Fundation (foundation abide) only Adjust the layout member to get the contact view in your desired layout and implement the `_form.scss` (CommunicationBundle/Resources/scss) in your foundation `app.scss`.

NOTE: The contact view uses custom symfony form row and form errors templates!

<a name="slack-service"/>

## Slack Service
Send a message to any given Slack channel.

```php
$slackService = $this->container->get('docono.communication.slack');

$slackService->setWebhook('webhookURL')->submitMessage('#channelName', 'botName', 'message); 
```