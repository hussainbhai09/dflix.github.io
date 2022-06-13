<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('duration',null,array());
        $builder->add('pack',null,array());
        $builder->add('infos',null,array());
        $builder->add('status' ,ChoiceType::class, array(
                'choices' => array(
                    "Paid" => "paid",
                    "UnPaid" => "unpaid",
                    "Pendding" => "pendding",
                )));
        $builder->add('transaction',null,array());
        $builder->add('email',null,array());
        $builder->add('started',null,array());
        $builder->add('expired',null,array());
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));

    }
    public function getName()
    {
        return 'Subscription';
    }
}
?>