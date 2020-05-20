<?php

namespace App\Form;

use App\Entity\MetaTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [ 'required' => false ])
            ->add('content', null, [ 'required' => false ])
            ->add('delete', ButtonType::class, ['attr' => ['class'=>'button button-small remove-this-field']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MetaTag::class,
        ]);
    }
}
