<?php
namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('name',TextType::class,array("label"=>"Full name"))
            ->add('file')
        ;
        $builder->add('save', SubmitType::class ,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'user';
    }
}
?>
