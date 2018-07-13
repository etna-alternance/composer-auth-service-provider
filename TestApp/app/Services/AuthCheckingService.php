<?php

namespace TestApp\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use ETNA\Auth\Services\AuthCheckingService as BaseAuthCheckingService;

class AuthCheckingService extends BaseAuthCheckingService
{
}
