<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\User;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security\UserAuthenticator;

class InvoiceType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            
            ->add('client', EntityType::class,[
                'class' => Client::class,
                'choice_label' => 'name',
                'query_builder' => function (ClientRepository $cr) use ($user) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('c.name', 'ASC');
                },
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date de la facture',
            ])
            //->add('status')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'user' => null,
        ]);
    }
}
