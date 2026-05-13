<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Client;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;

class InvoiceType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
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
           
            
           
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}

        