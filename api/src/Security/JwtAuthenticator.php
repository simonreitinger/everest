<?php
/**
 * Created by PhpStorm.
 * User: simonreitinger
 * Date: 2019-01-17
 * Time: 11:41
 */

namespace App\Security;

use App\HttpKernel\ApiProblemResponse;
use Crell\ApiProblem\ApiProblem;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var JWTEncoderInterface
     */
    private $tokenEncoder;

    /**
     * JwtAuthenticator constructor.
     * @param JWTEncoderInterface $tokenEncoder
     */
    public function __construct(JWTEncoderInterface $tokenEncoder)
    {
        $this->tokenEncoder = $tokenEncoder;
    }

    /**
     * @param Request $request
     * @return bool|void
     */
    public function supports(Request $request)
    {
        return false;
    }

    /**
     * @param Request $request
     * @return bool|false|mixed|string|string[]|null
     */
    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer','Authorization');

        $token = $extractor->extract($request);

        if (!$token)
        {
            return '';
        }

        return $token;
    }

    /**
     * @param mixed $credentials (user token)
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try
        {
            $data = $this->tokenEncoder->decode($credentials);
        }
        catch (JWTDecodeFailureException $e)
        {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        return $userProvider->loadUserByUsername($data['username']);
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|void|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $token->setAttribute('authenticator', get_called_class());
        return null;
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return ApiProblemResponse|Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new ApiProblemResponse((new ApiProblem())->setStatus(Response::HTTP_UNAUTHORIZED));
    }
}
