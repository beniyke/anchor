<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Services\AccountService;
use App\Account\Views\Models\ChangePhotoViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class ChangephotoController extends BaseController
{
    public function index(): Response
    {
        $change_photo_view_model = new ChangePhotoViewModel($this->container, $this->user_view_model);

        return $this->asView('profile.change-photo', compact('change_photo_view_model'));
    }

    public function update(AccountService $service): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $photo_uploaded = $service->updateUserPhoto($this->auth->user(), $this->request->validated());

        if (! $photo_uploaded) {
            $this->flash->error('Photo upload failed.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('Photo successfully uploaded.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
