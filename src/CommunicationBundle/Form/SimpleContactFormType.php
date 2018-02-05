<?php

namespace CommunicationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SimpleContactFormType extends AbstractType
{

    function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'form.firstname.label',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^[\p{L} ]*$/u'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label'    => 'form.email.label',
                'attr'     => [
                    'placeholder' => 'example@example.com'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('message', TextareaType::class, [
                'label'    => 'form.message.label',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^[\p{L}\p{N} \-\+\@\.\,\:\!\?\"\']*$/u'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
