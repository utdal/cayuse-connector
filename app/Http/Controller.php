<?php

namespace App\Http;

use App\Files\CsvReader;
use App\Http\Components\UserAffiliationSearch;
use App\Http\Components\UserSearch;
use App\Http\Components\UserTrainingLoader;
use App\Http\Components\UserTrainingSearch;
use App\Http\Components\UserTrainingTypesSearch;
use App\Http\Concerns\HasViews;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function userAffiliationSearch(Request $request): Response
    {
        $user_query = $request->query->get('user_query');

        $result = (new UserAffiliationSearch())->search($user_query);

        return new JsonResponse($result);
    }

    public function userTrainingSearch(Request $request): Response
    {
        $user_query = $request->query->get('user_query');

        $result = (new UserTrainingSearch())->search($user_query);

        return new JsonResponse($result);
    }

    public function userTrainingTypes(Request $request): Response
    {
        return new JsonResponse([
            'training_types' => (new UserTrainingTypesSearch())->search('', true)['training-types'] ?? [],
        ]);
    }

    public function userTrainingLoad(Request $request): StreamedResponse
    {
        $reader = new CsvReader($request->files->get('training'));
        $loader = new UserTrainingLoader($request->request->get('training_type'));
        $count = $reader->count();

        return new StreamedResponse(function() use ($reader, $loader, $count) {
            foreach ($reader->rows as $i => $user_training) {
                $result = $loader->load($user_training);
                echo json_encode($result) . (($i < $count) ? "\n" : '');
                ob_flush();
                flush();
            }
        }, 200, [
            'X-Accel-Buffering' => 'no' // disabled nginx FastCGI buffering
        ]);
    }
}