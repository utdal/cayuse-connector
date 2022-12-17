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
        '/api/v1/role' => 'roleSearch',
        '/api/v1/unit' => 'unitSearch',
        '/api/v1/user' => 'userSearch',
        '/api/v1/user/load' => 'userLoad',
        '/api/v1/user_account' => 'userAccountSearch',
        '/api/v1/user_affiliation' => 'userAffiliationSearch',
        '/api/v1/user_affiliation/load' => 'userAffiliationLoad',
        '/api/v1/user_role' => 'userRoleSearch',
        '/api/v1/user_role/load' => 'userRoleLoad',
        '/api/v1/user_training' => 'userTrainingSearch',
        '/api/v1/user_training/load' => 'userTrainingLoad',
        '/api/v1/user_training_types' => 'userTrainingTypes',
        '/api/v1/job/status' => 'jobStatus',
        '/api/v1/job/report' => 'jobReport',
    ];

    public function handle(): Response
    {
        $this->request = Request::createFromGlobals();

        try {
            $this->response = call_user_func($this->action(), $this->request);
        } catch (Exception $e) {
            $this->response = new Response(
                $e->getMessage() ?: 'An error occurred',
                ($e->getCode() >= 100 && $e->getCode() < 600) ? $e->getCode() : 500,
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