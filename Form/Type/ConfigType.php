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
            ->add('site_id', 'hidden')
            ->add('token', 'text', array(
                'label' => '認証トークン',
                'required' => true,
                'attr' => array(
                    'maxlength' => $config['stext_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('url', 'text', array(
                'label' => 'サイトのURL',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Url(),
                ),
            ))
            ->add('name', 'text', array(
                'label' => 'サイト名',
                'required' => true,
                'attr' => array(
                    'maxlength' => $config['stext_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('email', 'email', array(
                'required' => true,
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

                    return;
                }
                $sites = $app['kaiu.service.api']->getSiteList($token);

                $url = $data->getUrl();
                if (empty($url)) {
                    return;
                }
                if (!function_exists('array_column')) {
                    $siteUrl = array_map(function($element) {
                        return $element['url'];
                    }, $sites);
                } else {
                    $siteUrl = array_column($sites, 'url');
                }

                $index = array_search($url, $siteUrl);
                if ($index !== false) {
                    $form['url']->addError(new FormError('このユーザーのウェブサイトは既に存在しています！'));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\KaiUConnection\Entity\Config',
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
