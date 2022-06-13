<?php
namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('name', TextType::class, array(
    'disabled' => true,
))
            ->add('type', TextType::class, array(
    'disabled' => true,
))
            ->add('email', TextType::class, array(
    'disabled' => true,
))
            ->add('enabled',null,array())
        ;
        $builder->add('save', SubmitType::class,array("label"=>"SAVE USER"));

    }
    public function getName()
    {
        return 'user';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'csrf_token_id'   => 'task_item',
        ));
    }
}
?>
