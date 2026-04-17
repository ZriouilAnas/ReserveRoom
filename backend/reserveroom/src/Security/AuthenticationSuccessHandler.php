<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private JWTTokenManagerInterface $jwtManager) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $jwt = $this->jwtManager->create($token->getUser());

        $response = new JsonResponse(['message' => 'Login successful',
            'token' => $jwt
    ]);
        $response->headers->setCookie(Cookie::create('BEARER')
            ->withValue($jwt)
            ->withHttpOnly(true)
            ->withSecure(true) 
            ->withSameSite('None')
            ->withPath('/')
            ->withExpires(time() + 3600)
        );

        return $response;
    }
}
