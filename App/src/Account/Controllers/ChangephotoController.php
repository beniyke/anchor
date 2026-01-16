<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Core\BaseController;
use App\Services\UserService;
use App\Validations\Form\ChangePhotoFormRequestValidation;
use App\Views\Models\ChangePhotoViewModel;
use Helpers\Http\Response;

class ChangephotoController extends BaseController
{
    public function index(): Response
    {
        $change_photo_view_model = new ChangePhotoViewModel($this->container, $this->user_view_model);

        return $this->asView('profile.change-photo', compact('change_photo_view_model'));
    }

    public function update(ChangePhotoFormRequestValidation $validator, UserService $service): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->fullRoute());
        }

        $formdata = $this->request->file();
        $validator->validate($formdata);

        if ($validator->has_error()) {
            $this->flash->withInput($formdata, $validator->errors());

            return $this->response->redirect($this->request->fullRoute());
        }

        $photo_uploaded = $service->updateUserPhoto($this->auth->user(), $validator->getRequest());

        if (! $photo_uploaded) {
            $this->flash->error('Photo upload failed.');

            return $this->response->redirect($this->request->fullRoute());
        }

        $this->flash->success('Photo successfully uploaded.');

        return $this->response->redirect($this->request->fullRoute());
    }
}
