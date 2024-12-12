<?php

namespace App\Controller;

use App\Service\WishListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/wishlist')]
class WishListController extends AbstractController
{
    private WishListService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Добавляет новое желание.
     *
     * @Route('', name: 'wishlist_add', methods: ['POST'])
     */
    public function addWish(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $data = json_decode($request->getContent(), true);

        $result = $this->wishlistService->addWish($session, $data);

        if ($result) {
            return $this->json(['success' => true, 'message' => 'Желание добавлено.'], Response::HTTP_CREATED);
        }

        return $this->json(['success' => false, 'message' => 'Не удалось добавить желание.'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Обновляет существующее желание.
     *
     * @Route('/{id}', name: 'wishlist_update', methods: ['PUT'])
     */
    public function updateWish(Request $request, int $id): JsonResponse
    {
        $session = $request->getSession();
        $data = json_decode($request->getContent(), true);

        $result = $this->wishlistService->updateWish($session, $id, $data);

        if ($result) {
            return $this->json(['success' => true, 'message' => 'Желание обновлено.']);
        }

        return $this->json(['success' => false, 'message' => 'Не удалось обновить желание.'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Получает конкретное желание.
     *
     * @Route('/{id}', name: 'wishlist_get', methods: ['GET'])
     */
    public function getWish(Request $request, int $id): JsonResponse
    {
        $session = $request->getSession();
        $wish = $this->wishlistService->getWish($session, $id);

        if ($wish) {
            return $this->json([
                'success' => true,
                'wish' => [
                    'id' => $wish->getId(),
                    'name' => $wish->getName(),
                    'description' => $wish->getDescription(),
                    'url' => $wish->getUrl(),
                ],
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Желание не найдено или доступ запрещен.'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Удаляет желание.
     *
     * @Route('/{id}', name: 'wishlist_delete', methods: ['DELETE'])
     */
    public function deleteWish(Request $request, int $id): JsonResponse
    {
        $session = $request->getSession();

        $result = $this->wishlistService->deleteWish($session, $id);

        if ($result) {
            return $this->json(['success' => true, 'message' => 'Желание удалено.']);
        }

        return $this->json(['success' => false, 'message' => 'Не удалось удалить желание.'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Получает все желания пользователя.
     *
     * @Route('', name: 'wishlist_get_all', methods: ['GET'])
     */
    public function getWishes(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $wishes = $this->wishlistService->getWishes($session);

        $wishesData = array_map(function ($wish) {
            return [
                'id' => $wish->getId(),
                'name' => $wish->getName(),
                'description' => $wish->getDescription(),
                'url' => $wish->getUrl(),
            ];
        }, $wishes);

        return $this->json([
            'success' => true,
            'wishes' => $wishesData,
        ]);
    }
}
