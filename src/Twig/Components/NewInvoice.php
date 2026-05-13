<?php

namespace App\Twig\Components;

use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]

#[AsLiveComponent]
final class NewInvoice 
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true, fieldName : 'invoiceForm')]
    public ?Invoice $invoice =null;

    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private EntityManagerInterface $entityManagerInterface,
        private FormFactoryInterface $formFactoryInterface,
        private Security $security
    ) {}

    protected function instantiateForm(): \Symfony\Component\Form\FormInterface
    {
        $invoice = $this->invoice ?? new Invoice();
        return $this->formFactoryInterface->create(InvoiceType::class, $invoice);
    }

    #[LiveAction]
    public function saveInvoice(): void
    {
        $this->submitForm();
        $user = $this->security->getUser();
        $form = $this->getForm();
        if ($form->isValid()) {
            /** @var Invoice $invoice */
            $invoice = $form->getData();
            $invoice->setUser($user);
            $this->entityManagerInterface->persist($invoice);
            $this->entityManagerInterface->flush();

            $this->invoice = new Invoice();
            $this->resetForm();
        }
    }
    public function getAllInvoices(): array
    {
        return $this->invoiceRepository->findby(['user' => $this->security->getUser()]);
    }
}

