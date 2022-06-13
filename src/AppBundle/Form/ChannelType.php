<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ChannelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title');
        $builder->add('label');
        $builder->add('sublabel');
        $builder->add('description');
        $builder->add('featured');
        $builder->add('enabled');
        $builder->add('comment');
        $builder->add('classification');
        $builder->add('website');
        $builder->add('tags');
        $builder->add('playas' ,ChoiceType::class, array(
                'choices' => array(
                        "Free"=> 1 ,
                        "Premuim"=> 2,
                        "Unlock with rewards Ads"=> 3
                )));   
        $builder->add("categories",EntityType::class,
                  array(
                        'class' => 'AppBundle:Category',
                        'expanded' => true,
                        "multiple" => "true",
                        'by_reference' => false
                      )
                  );
        $builder->add("countries",EntityType::class,
                  array(
                        'class' => 'AppBundle:Country',
                        'expanded' => true,
                        "multiple" => "true",
                        'by_reference' => false
                      )
                  );
        $builder->add('sourcetype' ,ChoiceType::class, array(
                'choices' => array(
                    "Youtube Url"  =>       1,
                    "m3u8 Url"     =>       2,
                    "MOV Url"      =>       3,
                    "MP4 Url"      =>       4,
                    "MKV Url"      =>       6,
                    "WEBM Url"     =>       7,
                    "Embed source" =>       8
                )));
        $builder->add('sourceurl');

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
        return 'Channel';
    }
}
?>