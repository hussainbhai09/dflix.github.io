<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('faq', CKEditorType::class, 
                    array(
                        'config_name' => 'user_config'
                    )
                );
        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'Faq';
    }
}
?>