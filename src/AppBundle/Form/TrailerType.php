<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class TrailerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type' ,ChoiceType::class, array(
                'choices' => array(
                    "Youtube Url"  =>       1,
                    "m3u8 Url"     =>       2,
                    "MOV Url"      =>       3,
                    "MP4 Url"      =>       4,
                    "MKV Url"      =>       6,
                    "WEBM Url"     =>       7,
                    "Embed source" =>       8,
                    "File (MP4/MOV/MKV/WEBM)" =>5
                )));

        

        $builder->add('url',UrlType::class,array("required"=>false));
        $builder->add("file",null,array("label"=>"","required"=>false));
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));

    }
    public function getName()
    {
        return 'Source';
    }
}
?>