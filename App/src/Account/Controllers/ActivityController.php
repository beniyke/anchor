<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use Activity\Services\ActivityManagerService;
use App\Account\Views\Models\ActivityLogViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class ActivityController extends BaseController
{
    public function index(ActivityManagerService $service): Response
    {
        $user = $this->auth->user();
        $page = $this->request->get('page', 1);
        $activities = $service->listUserActivities($user, $page);
        $activity_log_view_model = new ActivityLogViewModel($activities);

        return $this->asView('activity.list', compact('activity_log_view_model'));
    }
}
