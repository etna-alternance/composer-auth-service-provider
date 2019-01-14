<?php

namespace TestApp\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ETNA\Auth\Services\AuthCookieService;

class BaseController extends Controller
{
    /**
     * @Route("/restricted", methods={"GET"}, name="restricted")
     */
    public function home(AuthCookieService $auth, Request $req)
    {
        $auth->userHasGroup($req, "adm");
        return new JsonResponse($req->attributes->get("auth.user"), 200);
    }

    /**
     * @Route("/restricted", methods={"OPTIONS"}, name="options_restricted")
     */
    public function optionsRestricted(AuthCookieService $auth, Request $req)
    {
        return new JsonResponse($req->attributes->get("auth.user"), 200);
    }
}
