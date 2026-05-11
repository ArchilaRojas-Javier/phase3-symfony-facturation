<?php

namespace App\Twig\Components;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;

#[AsLiveComponent]
final class NewClient extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true, fieldName: 'clientForm')]
    public ?Client $client = null;

    public function __construct(
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {}

    protected function instantiateForm(): \Symfony\Component\Form\FormInterface
    {
        $client = $this->client ?? new Client();
        return $this->createForm(ClientType::class, $client);
    }

    #[LiveAction]
    public function saveClient(): void
    {
        $this->submitForm();
        $user = $this->getUser();
        $form = $this->getForm();
        if ($form->isValid()) {
            /** @var Client $client */
            $client = $form->getData();
            $client->setUser($user);
            $this->entityManagerInterface->persist($client);
            $this->entityManagerInterface->flush();

            $this->client = new Client();
            $this->resetForm();
        }
    }
    public function getAllClients(): array
    {
        return $this->clientRepository->findby(['user' => $this->getUser()]);
    }
}
