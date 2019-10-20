<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AbController extends AbstractController
{

    /**
     * @return array
     *
     * In this method we show a list of actions for which we want to enable AB-tests
     */
    public function getAbTestableActions()
    {
        return ['index'];
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param string|null $hypothesis
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * Just add last nullable argument to desired action and you will get AB-hypothesis within it
     *
     */
    public function index(Request $request, ?string $hypothesis = null)
    {

        return $this->json(['data' => sprintf('seems like you are being tested for hypothesis %s', $hypothesis)]);
    }
}
