<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WebSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title',null,array());
        $builder->add('subtitle',null,array());
        $builder->add('sitedescription',null,array());
        $builder->add('sitekeywords',null,array());
        $builder->add('header',null,array());
        $builder->add('themoviedbkey',null,array());
        $builder->add('themoviedblang',null,array());
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("file",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("file",null,array("label"=>"","required"=>true));
            }
        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();
            if ($article and null !== $article->getId()) {
                 $form->add("favfile",null,array("label"=>"","required"=>false));
            }else{
                 $form->add("favfile",null,array("label"=>"","required"=>true));
            }
        });
        $builder->add('save', SubmitType::class ,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'WebSettings';
    }
}
?>