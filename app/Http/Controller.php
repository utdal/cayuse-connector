<?php

namespace App\Http;

use App\Files\CsvReader;
use App\Http\Components\{
    JobChecker,
    UnitSearch,
    UserAffiliationLoader,
    UserAffiliationSearch,
    UserAccountSearch,
    UserLoader,
    UserRoleLoader,
    UserRoleSearch,
    UserSearch,
    UserTrainingLoader,
    UserTrainingSearch,
    UserTrainingTypesSearch,
};
use App\Http\Concerns\HasViews;
use App\Models\UserRoleCollection;
use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Request,
    Response,
    StreamedResponse,
};

class Controller
{
    use HasViews;

    public function index(Request $request): Response
    {
        return $this->view('index', request: $request);
    }

    public function roleSearch(): JsonResponse
    {
        return new JsonResponse([
            'roles' => array_map('trim', explode(',', getenv('CAYUSE_ROLES') ?? ''))
        ]);
    }

    public function userSearch(Request $request): JsonResponse
    {
        $result = (new UserSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function userAccountSearch(Request $request): JsonResponse
    {
        $result = (new UserAccountSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function userAffiliationSearch(Request $request): JsonResponse
    {
        $result = (new UserAffiliationSearch())->search($request->query->all());

        return new JsonResponse($result);
    }

    public function userRoleSearch(Request $request): JsonResponse
    {
        $result = (new UserRoleSearch())->search($request->query->all());

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
        $filter_columns = $request->request->getBoolean('filter_columns', true);
        $result = (new UserLoader())->load($users_file, $filter_columns);
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

    public function userRoleLoad(Request $request): JsonResponse
    {
        $roles_file = $request->files->get('roles');

        $user_roles = new UserRoleCollection([
            'roles' => $request->request->all('selected_roles'),
            'unit_codes' => $request->request->all('selected_unit_codes'),
            'subunits' => $request->request->all('selected_subunits'),
        ]);

        $result = (new UserRoleLoader())->load($roles_file, $user_roles);
        $result['count'] = (new CsvReader($roles_file))->count();

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