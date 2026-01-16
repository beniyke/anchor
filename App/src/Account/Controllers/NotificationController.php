<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Core\BaseController;
use App\Services\NotificationService;
use App\Views\Models\NotificationLogViewModel;
use Helpers\Http\Response;

class NotificationController extends BaseController
{
    public function index(NotificationService $service): Response
    {
        $user = $this->auth->user();
        $page = $this->request->get('page', 1);
        $notifications = $service->listNotifications($user, $page);
        $notification_log_view_model = new NotificationLogViewModel($notifications);

        return $this->asView('notification.list', compact('notification_log_view_model'));
    }

    public function destroy(NotificationService $service): Response
    {
        if (! $this->request->isDelete()) {
            return $this->response->redirect($this->request->callback());
        }

        $notification_cleared = $service->clearUserNotifications($this->auth->user());

        if (! $notification_cleared) {
            $this->flash->error('Notification could not be cleared.');

            return $this->response->redirect($this->request->callback());
        }

        $this->flash->success('Notification successfully cleared.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
