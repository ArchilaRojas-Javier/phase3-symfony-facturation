<?php

namespace App\Twig\Components;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Entity\User;

#[AsLiveComponent]
final class NewProduct extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true, fieldName: 'productForm')]
    public ?Product $product = null;

    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {}

    protected function instantiateForm(): \Symfony\Component\Form\FormInterface
    {
        $product = $this->product ?? new Product();
        return $this->createForm(ProductType::class, $product);
    }

    #[LiveAction]
    public function saveProduct(): void
    {

        $this->submitForm();
        $user = $this->getUser();

        $form = $this->getForm();
        if ($form->isValid()) {
            /** @var Product $product */
            $product = $form->getData();
            $product->setUser($user);
            $this->entityManagerInterface->persist($product);
            $this->entityManagerInterface->flush();

            $this->product = new Product();
            $this->resetForm();
        }
    }
    public function getAllProducts(): array
    {
        return $this->productRepository->findby(['user' => $this->getUser()]);
    }
}
