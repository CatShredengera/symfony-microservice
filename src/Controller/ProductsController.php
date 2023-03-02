<?php

namespace App\Controller;

use App\Cache\PromotionCache;
use App\DTO\LowestPriceEnquire;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Service\Serializer\DTOSerializer;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $repository,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/products/{id}/lowest-price', name: 'lowest-price', methods: 'POST')]
    public function lowestPrice(
        Request $request,
        int $id,
        DTOSerializer $serializer,
        PromotionsFilterInterface $promotionsFilter,
        PromotionCache $promotionCache
    ): Response
    {
        /** @var LowestPriceEnquire $lowestPriceEnquire */
        $lowestPriceEnquire = $serializer->deserialize(
            $request->getContent(),
            LowestPriceEnquire::class,
            'json'
        );

        $product = $this->repository->findOrFail($id);

        $lowestPriceEnquire->setProduct($product);

        $promotions = $promotionCache->findValidForProduct($product, $lowestPriceEnquire->getRequestDate());

        $modifiedEnquire = $promotionsFilter->apply($lowestPriceEnquire, ...$promotions);

        $responseContent = $serializer->serialize($modifiedEnquire, 'json');

        return new JsonResponse(data: $responseContent, status: Response::HTTP_OK, json: true);
    }
}