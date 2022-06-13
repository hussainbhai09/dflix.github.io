<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('currency',null,array("label"=>"Currency Code"));
        $builder->add('paypal',ChoiceType::class, array(
                'choices' => array(
                   "Enable PayPal Payment"=> true,
                    "Disable PayPal Payment"=> false,
                )));
        $builder->add('paypalaccount',null,array("label"=>"PayPal Account"));
        $builder->add('paypalclientid',null,array("label"=>"PayPal Client id"));
        $builder->add('paypalclientsecret',null,array("label"=>"PayPal Client secret"));
        $builder->add('paypalsandbox',ChoiceType::class, array(
                'choices' => array(
                   "Enable Sandbox Mode" => true,
                    "Disable Sandbox Mode" => false,
                )));
        $builder->add('stripe',ChoiceType::class, array(
                'choices' => array(
                    "Enable Stripe Payment" => true,
                    "Disable Stripe Payment" => false,
                )));
        $builder->add('gpay',ChoiceType::class, array(
                'choices' => array(
                    "Enable Google Play Payment" => true,
                    "Disable Google Play Payment" => false,
                )));
        $builder->add('stripeapikey',null,array("label"=>"Stripe Api Key"));
        $builder->add('stripepublickey',null,array("label"=>"Stripe Public Key"));
        $builder->add('manual',ChoiceType::class, array(
                'choices' => array(
                    "Enable Cash Payment" => true,
                    "Disable Cash Payment" => false,
                )));
        $builder->add('cashaccount',null,array("label"=>"Cash Account Infos"));

        $builder->add('save', SubmitType::class,array("label"=>"SAVE"));
    }
    public function getName()
    {
        return 'Payment';
    }
}
?>