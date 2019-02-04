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
use App\Security\JwtAuthenticator;
use Crell\ApiProblem\ApiProblem;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Route("/auth")
 *
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends AbstractController
{

    /**
     * @var JwtAuthenticator $auth
     */
    private $auth;

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
     * @param JwtAuthenticator $authenticator
     * @param JWTEncoderInterface $jwtEncoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(JwtAuthenticator $authenticator,
                                JWTEncoderInterface $jwtEncoder,
                                UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $entityManager)
    {
        $this->auth = $authenticator;
        $this->jwtEncoder = $jwtEncoder;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login")
     *
     * @param Request $request
     * @return JsonResponse|ApiProblemResponse
     */
    public function login(Request $request)
    {
        $credentials = json_decode($request->getContent(), true);

        $user = $this->entityManager->getRepository(User::class)->findOneByUsername($credentials['username']);

        if (!$user) {
            return new ApiProblemResponse((new ApiProblem('Invalid credentials'))->setStatus(Response::HTTP_BAD_REQUEST));
        }

        $passwordValid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        if (!$passwordValid) {
            return new ApiProblemResponse((new ApiProblem('Invalid credentials'))->setStatus(Response::HTTP_BAD_REQUEST));
        }

        try
        {
            $token = $this->jwtEncoder->encode(['username' => $credentials['username']]);
        }
        catch (JWTEncodeFailureException $e) {
            return new ApiProblemResponse((new ApiProblem('There was a problem encoding the token.'))->setStatus(Response::HTTP_BAD_REQUEST));
        }

        return new JsonResponse(['token' => $token]);
    }
}
