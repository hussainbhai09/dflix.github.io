<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title',null,array());
        $builder->add('description',null,array());
        $builder->add('discount',null,array());
        $builder->add('duration',null,array());
        $builder->add('price',null,array());
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));

    }
    public function getName()
    {
        return 'Pack';
    }
}
?>