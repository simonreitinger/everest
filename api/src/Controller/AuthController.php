<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-02-04
 * Time: 13:36
 */

namespace App\Controller;

use App\Entity\User;
use App\HttpKernel\ApiProblemResponse;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/auth")
 *
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends ApiController
{
    /**
     * @var JWTEncoderInterface $jwtEncoder
     */
    private $jwtEncoder;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * AuthController constructor.
     * @param JWTEncoderInterface $jwtEncoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(JWTEncoderInterface $jwtEncoder,
                                UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $entityManager)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse|ApiProblemResponse
     * @throws JWTEncodeFailureException
     */
    public function login(Request $request)
    {
        $credentials = $this->getRequestContentAsJson($request);

        $user = $this->entityManager->getRepository(User::class)->findOneByUsername($credentials['username']);

        if (!$user) {
            $this->createApiProblemResponse('Invalid credentials', Response::HTTP_BAD_REQUEST);
        }

        $passwordValid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        if (!$passwordValid) {
            return $this->createApiProblemResponse('Invalid credentials', Response::HTTP_BAD_REQUEST);
        }

        $token = $this->jwtEncoder->encode([
            'username' => $user->getUsername()
        ]);

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/token", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse|ApiProblemResponse
     * @throws JWTEncodeFailureException
     * @throws JWTDecodeFailureException
     */
    public function refreshToken(Request $request)
    {
        $jwt = $this->getJwtFromRequest($request);

        $token = $this->jwtEncoder->decode($jwt);

        // return a new token for 15 minutes of inactivity
        if ($token['exp'] > strtotime('now - 15 minutes')) {
            unset($token['exp'], $token['iat']);
            $newToken = $this->jwtEncoder->encode($token);

            return new JsonResponse(['token' => $newToken]);
        }

        return $this->createApiProblemResponse('The token could not be refreshed.', Response::HTTP_UNAUTHORIZED);
    }
}
