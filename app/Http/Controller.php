<?php

namespace App\Http;

use App\Files\CsvReader;
use App\Http\Components\JobChecker;
use App\Http\Components\UnitSearch;
use App\Http\Components\UserAffiliationLoader;
use App\Http\Components\UserAffiliationSearch;
use App\Http\Components\UserLoader;
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

    public function userSearch(Request $request): JsonResponse
    {
        $result = (new UserSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function userAffiliationSearch(Request $request): JsonResponse
    {
        $result = (new UserAffiliationSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function userTrainingSearch(Request $request): JsonResponse
    {
        $result = (new UserTrainingSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function unitSearch(Request $request): JsonResponse
    {
        $result = (new UnitSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function userTrainingTypes(Request $request): JsonResponse
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

    public function userLoad(Request $request): JsonResponse
    {
        $users_file = $request->files->get('users');
        $result = (new UserLoader())->load($users_file);
        $result['count'] = (new CsvReader($users_file))->count();

        return new JsonResponse($result);
    }

    public function userAffiliationLoad(Request $request): JsonResponse
    {
        $affiliations_file = $request->files->get('affiliations');
        $result = (new UserAffiliationLoader())->load($affiliations_file);
        $result['count'] = (new CsvReader($affiliations_file))->count();

        return new JsonResponse($result);
    }

    public function jobStatus(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        $job_id = $request->query->get('jobId');
        $result = (new JobChecker('status', $type))->check($job_id);

        return new JsonResponse($result);
    }

    public function jobReport(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        $job_id = $request->query->get('jobId');
        $result = (new JobChecker('report', $type))->check($job_id);

        return new JsonResponse($result);
    }
}