<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-05
 * Time: 15:22
 */

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route("/user")
 */
class UserController extends ApiController
{
    /**
     * @var JWTEncoderInterface $jwtEncoder
     */
    private $jwtEncoder;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * UserController constructor.
     * @param JWTEncoderInterface $jwtEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $entityManager)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function getUserDetail(Request $request)
    {
        $jwt = $this->getJwtFromRequest($request);
        $decoded = $this->jwtEncoder->decode($jwt);

        $user = $this->entityManager->getRepository(User::class)->findOneByUsername($decoded['username']);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($user);
    }

    /**
     * @Route("/all", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getUsernames()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            throw new NotFoundHttpException();
        }

        $data = [];

        foreach ($users as $user) {
            $data[] = $user->getUsername();
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/update", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUser(Request $request)
    {
        $payload = $this->getRequestContentAsJson($request);

        $jwt = $this->getJwtFromRequest($request);
        $decoded = $this->jwtEncoder->decode($jwt);

        $validCount = 0;

        foreach ($payload as $key => $value) {
            if (in_array($key, ['email', 'firstName', 'lastName'])) {
                $validCount++;
            }
        }

        if ($validCount === count($payload)) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findOneByUsername($decoded['username']);

            $user
                ->setEmail($payload['email'])
                ->setFirstName($payload['firstName'])
                ->setLastName($payload['lastName'])
            ;

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse($user);
        }

        return $this->createApiProblemResponse('Invalid data', Response::HTTP_BAD_REQUEST);
    }
}
