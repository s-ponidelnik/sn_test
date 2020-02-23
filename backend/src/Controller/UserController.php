<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
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
     * @throws \Exception
     */
    public function users(Request $request, EntityManagerInterface $em): JsonResponse
    {
        ;
        $username = $request->get('username', null);
        $showHidden = $request->get('show_hidden', true);
        //simple validate
        if (empty($username)) {
            return $this->json(['status' => ['code' => 400, 'text' => 'username is required']]);
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $users = $userRepository->findHiddenByLikeUsername($username, $showHidden);

        //search in github only when we have no equals result's in db, case 60 request per hour github limit
        if (!isset($users[$username])) {
            $data = \GithubApiHelper::showUser($username);
            if (!empty($data)) {
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
     * @Route("/hide_user", name="hide_user")
     */
    public function hideUser(Request $request): JsonResponse
    {
        $id = $request->get('id', null);
        $showHidden = $request->get('hide', false);
        if (empty($id)) {
            return $this->json(['status' => ['code' => 400, 'text' => 'id is required']]);
        }
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['status' => ['code' => 404, 'text' => 'user not found']]);
        }
        return $this->json(['status' => ['code' => 200, 'text' => 'OK'], 'result' => (int)$user->getIsHide()]);
    }
}
