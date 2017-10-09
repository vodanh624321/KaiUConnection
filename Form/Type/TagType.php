<?php
namespace Plugin\KaiUConnection\Form\Type;

use Plugin\KaiUConnection\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TagType extends AbstractType
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
        $config = $this->app['config']['KaiUConnection'];
        $builder
            ->add('tag_name', 'text', array(
                'label' => 'Tag name',
                'required' => false,
                'attr' => array(
                    'maxlength' => $config['const']['max_value_length'],
                ),
                'constraints' => array(
//                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $config['const']['max_value_length'])),
                ),
            ))
            ->add('tag_value', 'text', array(
                'label' => 'Tag value',
                'required' => false,
                'attr' => array(
                    'maxlength' => $config['const']['max_value_length'],
                ),
                'constraints' => array(
//                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $config['const']['max_value_length'])),
                ),
            ));
    }

    /**
     * configureOptions.
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\KaiUConnection\Entity\Tag',
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tag';
    }
}
