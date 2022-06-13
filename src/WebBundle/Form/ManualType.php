<?php
namespace WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
class ManualType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('transaction',null,array("label"=>"Payment transaction details :"));
        $builder->add('infos',null,array("label"=>"Payment transaction details :"));
        $builder->add('file');

    }
    public function getName()
    {
        return 'Subscription';
    }
}
?>