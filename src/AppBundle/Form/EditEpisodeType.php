<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditEpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('description');

        $builder->add('playas' ,ChoiceType::class, array(
                'choices' => array(
                    "Free"=> 1 ,
                    "Premuim"=> 2,
                    "Unlock with rewards Ads"=> 3
                )));   
        $builder->add('downloadas' ,ChoiceType::class, array(
                'choices' => array(
                    "Free"=> 1 ,
                    "Premuim"=> 2,
                    "Unlock with rewards Ads"=> 3
                )));        
        $builder->add('duration');
        $builder->add('enabled');
        $builder->add("file",null,array("label"=>"","required"=>false));
        $builder->add('save',SubmitType::class,array("label"=>"SAVE"));

    }
    public function getName()
    {
        return 'episode';
    }
}
?>