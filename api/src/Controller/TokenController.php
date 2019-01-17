<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 09:33
 */

namespace App\Controller;

use App\Entity\User;
use App\HttpKernel\ApiProblemResponse;
use App\Security\JwtAuthenticator;
use Crell\ApiProblem\ApiProblem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class TokenController
 * @package App\Controller
 *
 * @Route("/api/token")
 */
class TokenController extends ApiController
{
    private $passwordEncoder;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var JwtAuthenticator
     */
    private $jwtAuthenticator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TokenController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTEncoderInterface $jwtEncoder
     * @param JwtAuthenticator $jwtAuthenticator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        JWTEncoderInterface $jwtEncoder,
        JwtAuthenticator $jwtAuthenticator,
        EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->jwtAuthenticator = $jwtAuthenticator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        switch ($request->getMethod())
        {
            case 'GET':
                return $this->getStatus($request);
            case 'POST':
                return $this->handleLogin($request);
            case 'DELETE':
                return $this->handleLogout($request);
        }

        return new Response(null, Response::HTTP_METHOD_NOT_ALLOWED);
    }

    private function getStatus(Request $request)
    {
        $token = $this->jwtAuthenticator->getCredentials($request);

        try
        {
            $user = $this->entityManager->getRepository('App:User')->findOneBy(['username' => $this->jwtEncoder->decode($token)['username']]);
        }
        catch (JWTDecodeFailureException $e)
        {
            return new Response((new ApiProblem())->setStatus(Response::HTTP_UNAUTHORIZED)->asJson(), 500);
        }

        return new JsonResponse(['username' => $user->getUsername(), 'login' => true]);
    }

    private function handleLogin(Request $request)
    {
        $token = $this->jwtAuthenticator->getCredentials($request);

        try
        {
            $user = $this->entityManager->getRepository('App:User')->findOneBy(['username' => $this->jwtEncoder->decode($token)['username']]);

            // user found
            return new ApiProblemResponse(
                (new ApiProblem('User is already logged in'))->setStatus(Response::HTTP_BAD_REQUEST)
            );
        }
        // token not found or invalid
        catch (JWTDecodeFailureException $e)
        {
            return new Response((new ApiProblem())->setStatus(Response::HTTP_UNAUTHORIZED)->asJson(), 500);
        }


        $payload = json_decode($request->getContent(), true);
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('App:User')->findOneBy(['username' => $payload['username']]);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $payload['password']))
        {
            return new ApiProblemResponse(
                (new ApiProblem('Incorrect username or password'))->setStatus(Response::HTTP_BAD_REQUEST)
            );
        }

        $token = $this->jwtEncoder->encode(['username' => $payload['username']]);

        $response = new JsonResponse(['username' => $payload['username']], 200, [
            'Authorization' => 'Bearer '. $token
        ]);

        // $this->jwtManager->addToken($request, $response, $payload['username']);

        return $response;
    }

    private function handleLogout(Request $request)
    {
        $token = $this->jwtAuthenticator->getCredentials($request);

        try
        {
            $user = $this->entityManager->getRepository('App:User')->findOneBy(['username' => $this->jwtEncoder->decode($token)['username']]);

        }
        catch (JWTDecodeFailureException $e)
        {
            return new Response((new ApiProblem())->setStatus(Response::HTTP_UNAUTHORIZED)->asJson(), 500);
        }
    }

}
