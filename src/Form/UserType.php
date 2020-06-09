<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{

    /** @var TranslatorInterface */
    protected $translator = null;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [ 'label' => $this->translator->trans('Username') ])
            ->add('password', PasswordType::class, [ 'label' => $this->translator->trans('Password') ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_SEO' => 'ROLE_SEO',
                    'ROLE_MANAGER' => 'ROLE_MANAGER',
                    'ROLE_PRODUCTS' => 'ROLE_PRODUCTS',
                    'ROLE_PROJECTS' => 'ROLE_PROJECTS',
                    'ROLE_PARTNERS' => 'ROLE_PARTNERS',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => $this->translator->trans('Roles')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
