<?php

namespace App\Controller;

use App\Service\AdminService;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * AdminController handles administrative actions.
 */
class AdminController extends AbstractController
{
    private $adminService;
    private $authService;

    /**
     * Constructor to inject services.
     *
     * @param AdminService $adminService
     * @param AuthService $authService
     */
    public function __construct(AdminService $adminService, AuthService $authService)
    {
        $this->adminService = $adminService;
        $this->authService = $authService;
    }

    /**
     * Set the status of a user profile.
     *
     * @Route("/admin/set_status", name="admin_set_status", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setStatus(Request $request): JsonResponse
    {
        $profileId = $request->request->get('profile_id');
        $status = $request->request->get('status');
        $sessionId = $request->request->get('session_id');

        if (!$profileId || !$status || !$sessionId) {
            return $this->json(['error' => 'Missing parameters'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->authService->getUserBySession();

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        $allowedStatuses = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MODERATOR'];
        if (!in_array($status, $allowedStatuses)) {
            return $this->json(['error' => 'Invalid status'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $result = $this->adminService->setStatus($profileId, $status);

        if (!$result) {
            return $this->json(['error' => 'Failed to set status'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }


    /**
     * Get the status of a user profile.
     *
     * @Route("/admin/get_status/{profile_id}", name="admin_get_status", methods={"GET"})
     *
     * @param string $profileId
     * @return JsonResponse
     */
    public function getStatus(string $profileId): JsonResponse
    {
        // Get the status using AdminService
        $status = $this->adminService->getStatus($profileId);

        if ($status === null) {
            // Return error if profile not found
            return $this->json(['error' => 'Profile not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json(['status' => $status]);
    }

    /**
     * Get a user profile.
     *
     * @Route("/admin/get_profile", name="admin_get_profile", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        // Retrieve profile_id and session_id from the request
        $profileId = $request->request->get('profile_id');
        $sessionId = $request->request->get('session_id');

        // Authenticate the session
        $user = $this->authService->getUserBySession($sessionId);

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            // Return error if not authenticated or not an admin
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Get the profile using AdminService
        $profile = $this->adminService->getProfile($profileId);

        if ($profile === null) {
            // Return error if profile not found
            return $this->json(['error' => 'Profile not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($profile);
    }

    /**
     * Update a user profile.
     *
     * @Route("/admin/update_profile", name="admin_update_profile", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        // Retrieve session_id from the request
        $sessionId = $request->request->get('session_id');

        // Authenticate session
        $user = $this->authService->getUserBySession($sessionId);

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            // Return an error if not authenticated or lacks admin privileges
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Retrieve the request body
        $content = $request->getContent();
        $data = json_decode($content, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Get profile_id and profile data from the decoded JSON
        $profileId = $data['profile_id'] ?? null;
        $profileData = $data['profile'] ?? null;

        if (!$profileId || !is_array($profileData)) {
            return $this->json(['error' => 'Invalid input data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Update the profile using AdminService
        $updatedProfile = $this->adminService->updateProfile($profileId, $profileData);

        if ($updatedProfile === null) {
            // Return an error if the update fails
            return $this->json(['error' => 'Profile update failed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true, 'profile' => $updatedProfile]);
    }

    /**
     * Delete a user profile.
     *
     * @Route("/admin/delete_profile", name="admin_delete_profile", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteProfile(Request $request): JsonResponse
    {
        // Retrieve profile_id and session_id from the request
        $profileId = $request->request->get('profile_id');
        $sessionId = $request->request->get('session_id');

        // Authenticate the session
        $user = $this->authService->getUserBySession($sessionId);

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            // Return error if not authenticated or not an admin
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Delete the profile using AdminService
        $result = $this->adminService->deleteProfile($profileId);

        if (!$result) {
            // Return error if deletion failed
            return $this->json(['error' => 'Profile deletion failed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }

    /**
     * Get a game by UUID.
     *
     * @Route("/admin/get_game", name="admin_get_game", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGame(Request $request): JsonResponse
    {
        // Retrieve UUID and session_id from the request
        $uuid = $request->request->get('UUID');
        $sessionId = $request->request->get('session_id');

        // Authenticate the session
        $user = $this->authService->getUserBySession($sessionId);

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            // Return error if not authenticated or not an admin
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Get the game using AdminService
        $game = $this->adminService->getGame($uuid);

        if ($game === null) {
            // Return error if game not found
            return $this->json(['error' => 'Game not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($game);
    }

    /**
     * Delete a game by UUID.
     *
     * @Route("/admin/delete_game", name="admin_delete_game", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteGame(Request $request): JsonResponse
    {
        // Retrieve UUID and session_id from the request
        $uuid = $request->request->get('UUID');
        $sessionId = $request->request->get('session_id');

        // Authenticate the session
        $user = $this->authService->getUserBySession($sessionId);

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            // Return error if not authenticated or not an admin
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Delete the game using AdminService
        $result = $this->adminService->deleteGame($uuid);

        if (!$result) {
            // Return error if deletion failed
            return $this->json(['error' => 'Game deletion failed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }

    /**
     * Update a game.
     *
     * @Route("/admin/update_game", name="admin_update_game", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGame(Request $request): JsonResponse
    {
        // Retrieve session_id from the request
        $sessionId = $request->request->get('session_id');

        // Authenticate session
        $user = $this->authService->getUserBySession($sessionId);

        if (!$user || !$this->isGranted('ROLE_ADMIN', $user)) {
            // Return an error if not authenticated or lacks admin privileges
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Retrieve the request content (JSON)
        $content = $request->getContent();
        $data = json_decode($content, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Get the UUID and game data from the decoded JSON
        $uuid = $data['UUID'] ?? null;
        $gameData = $data['game'] ?? null;

        if (!$uuid || !is_array($gameData)) {
            return $this->json(['error' => 'Invalid input data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Update the game using AdminService
        $updatedGame = $this->adminService->updateGame($uuid, $gameData);

        if ($updatedGame === null) {
            // Return an error if the update fails
            return $this->json(['error' => 'Game update failed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true, 'game' => $updatedGame]);
    }
}
