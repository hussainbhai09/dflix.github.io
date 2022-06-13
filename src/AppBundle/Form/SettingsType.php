<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('appname',null,array("label"=>"Application name"));
        $builder->add('appdescription',null,array("label"=>"Application description"));
        $builder->add('firebasekey',null,array("label"=>"Firebase Legacy server key"));
        $builder->add('googleplay',null,array("label"=>"Google Play App Url"));
        $builder->add('login',ChoiceType::class, array(
                'choices' => array(
                     "Login required" => true, 
                     "Login Optional"=> false,
                )));        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("file",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("file",null,array("label"=>"","required"=>true));
            }
        });
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'Settings';
    }
}
?>