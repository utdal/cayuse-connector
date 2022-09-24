<?php

namespace App\Http;

use App\Exceptions\NotFoundHttpException;
use App\Http\Controller;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    public Request $request;
    public Response $response;

    public array $routes = [
        '/' => 'index',
        '/api/v1/user_search' => 'userSearch',
        '/api/v1/user_training_search' => 'userTrainingSearch',
        '/api/v1/user_training/load' => 'userTrainingLoad',
        '/api/v1/user_training_types' => 'userTrainingTypes',
    ];

    public function handle(): Response
    {
        $this->request = Request::createFromGlobals();

        try {
            $this->response = call_user_func($this->action(), $this->request);
        } catch (Exception $e) {
            $this->response = new Response(
                $e->getMessage() ?: 'An error occurred',
                $e->getCode() ?: 500,
            );
        }

        return $this->response
            ->prepare($this->request)
            ->send();
    }

    public function action(): callable
    {
        $action = $this->routes[$this->request->getPathInfo()] ?? false;

        if (!$action) {
            throw new NotFoundHttpException();
        }

        return [new Controller(), $action];
    }
}