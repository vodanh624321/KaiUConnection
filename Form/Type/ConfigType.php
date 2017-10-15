<?php
namespace Plugin\KaiUConnection\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

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
        $config = $app['config'];
        $builder
            ->add('id', 'hidden')
            ->add('token', 'text', array(
                'label' => 'サイトトークン',
                'required' => true,
                'attr' => array(
                    'maxlength' => $config['stext_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('site_url', 'text', array(
                'label' => 'サイトのURL',
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Url(),
                ),
            ))
            ->add('site_name', 'text', array(
                'label' => 'サイト名',
                'required' => false,
                'attr' => array(
                    'maxlength' => $config['stext_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('email', 'email', array(
                'required' => false,
                'label' => 'Email',
                'attr' => array(
                    'maxlength' => $config['stext_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $event->getData();
                $token = $data->getToken();
                if (empty($token)) {
                    return;
                }
                $status = $app['kaiu.service.api']->checkToken($token);
                if (!$status) {
                    $form['token']->addError(new FormError('トークンが無効です。'));
                }
            });
    }

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
        return 'config';
    }
}
