<?php
namespace Plugin\KaiUConnection\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigType extends AbstractType
{
    /**
     * @var \Eccube\Application
     */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Build config type form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;
        $builder
            ->add('tags', 'collection', array(
                'type' => 'tag',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
//                'mapped' => false,
                'options' => array('label' => false),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\KaiUConnection\Entity\ConfigPlugin',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'config';
    }
}
