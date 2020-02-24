<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GithubApiHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $id = $request->get('id', null);
        if (empty($id)) {
            return $this->json(['status' => ['code' => 400, 'text' => 'id is required']]);
        }
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        return $this->json(['status' => ['code' => 200, 'text' => 'OK'], 'result' => $userRepository->findByIdAsArray($id)]);
    }

    /**
     * @Route("/users", name="users")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws Exception
     */
    public function users(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $username = $request->get('username', null);
        $showHidden = $request->get('show_hidden', false);
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        if (empty($username)) {
            return $this->showAllUsers($userRepository, $showHidden);
        }
        return $this->showSearchedUsers($userRepository, $username, $showHidden, $em);

    }

    /**
     * @param UserRepository $userRepository
     * @param string $username
     * @param bool $showHidden
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @throws Exception
     */
    private function showSearchedUsers(UserRepository $userRepository, string $username, bool $showHidden, EntityManagerInterface $em): JsonResponse
    {
        $users = $userRepository->findHiddenByLikeUsername($username, $showHidden);
        //search in github only when we have no equals result's in db, case 60 request per hour github limit
        if (!isset($users[$username])) {
            $data = GithubApiHelper::showUser($username);
            if (!empty($data)) {
                //save new user
                $user = new User();
                $user->setUsername($data['login']);
                $user->setAvatarUrl($data['avatar_url']);
                $createdAt = new DateTime($data['created_at']);
                $user->setCreatedAt($createdAt);
                $em->persist($user);
                $em->flush();
                $users[$data['login']] =
                    [
                        'username' => $data['login'],
                        'avatar_url' => $data['avatar_url'],
                        'created_at' => $data['created_at']
                    ];
            }
        }
        return $this->json(['status' => ['code' => 200, 'text' => 'OK'], 'result' => $users]);
    }

    /**
     * @param UserRepository $userRepository
     * @param bool $showHidden
     * @return JsonResponse
     */
    private function showAllUsers(UserRepository $userRepository, bool $showHidden): JsonResponse
    {
        return $this->json(
            [
                'status' => [
                    'code' => 200,
                    'text' => 'OK'],
                'result' => $userRepository->findAllHiddenAsArray($showHidden)
            ]);
    }

    /**
     * @Route("/hide_user", name="hide_user")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function hideUser(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id = $request->get('id', null);
        $hide = $request->get('hide', null);
        if (empty($id)) {
            return $this->json(['status' => ['code' => 400, 'text' => 'id is required']]);
        }
        if (is_null($hide)) {
            return $this->json(['status' => ['code' => 400, 'text' => 'hide is required']]);
        }
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['status' => ['code' => 404, 'text' => 'user not found']]);
        }
        $user->setIsHide((bool)$hide);
        $em->persist($user);
        $em->flush();
        return $this->json(['status' => ['code' => 200, 'text' => 'OK'], 'result' => (int)$user->getIsHide()]);
    }
}
