<?php

namespace App\Http;

use App\Http\Components\UserSearch;
use App\Http\Components\UserTrainingSearch;
use App\Http\Concerns\HasViews;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    use HasViews;

    public function index(Request $request): Response
    {
        return $this->view('index', request: $request);
    }

    public function userSearch(Request $request): Response
    {
        $user_query = $request->query->get('user_query');

        $result = (new UserSearch())->search($user_query);

        return new JsonResponse($result);
    }

    public function userTrainingSearch(Request $request): Response
    {
        $user_query = $request->query->get('user_query');

        $result = (new UserTrainingSearch())->search($user_query);

        return new JsonResponse($result);
    }
}