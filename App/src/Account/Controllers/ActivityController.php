<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Core\BaseController;
use App\Services\ActivityService;
use App\Views\Models\ActivityLogViewModel;
use Helpers\Http\Response;

class ActivityController extends BaseController
{
    public function index(ActivityService $service): Response
    {
        $user = $this->auth->user();
        $page = $this->request->get('page', 1);
        $activities = $service->listUserActivities($user, $page);
        $activity_log_view_model = new ActivityLogViewModel($activities);

        return $this->asView('activity.list', compact('activity_log_view_model'));
    }
}
