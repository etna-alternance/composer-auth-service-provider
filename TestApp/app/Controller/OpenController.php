<?php

namespace TestApp\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ETNA\Auth\Services\AuthCookieService;

class OpenController extends Controller
{
    /**
     * @Route("/open", methods={"GET"}, name="open")
     */
    public function home(AuthCookieService $auth, Request $req)
    {
        return new JsonResponse($req->attributes->get("auth.user"), 200);
    }
}
