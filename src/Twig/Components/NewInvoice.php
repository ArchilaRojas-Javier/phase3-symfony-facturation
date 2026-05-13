<?php

namespace App\Twig\Components;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;

#[AsLiveComponent]
final class NewInvoice extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true, fieldname : 'invoiceForm')]
    public ?Invoice $invoice =null;

    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {}

    protected function instantiateForm(): \Symfony\Component\Form\FormInterface
    {
        $invoice = $this->invoice ?? new Invoice();
        return $this->createForm(InvoiceType::class, $invoice);
    }

    #[LiveAction]
    public function saveInvoice(): void
    {
        $this->submitForm();
        $user = $this->getUser();
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
        return $this->invoiceRepository->findby(['user' => $this->getUser()]);
    }
}

