<?php

declare(strict_types=1);

namespace App\Account\Controllers;

use App\Account\Services\AccountService;
use App\Account\Views\Models\ProfileViewModel;
use App\Core\BaseController;
use Helpers\Http\Response;

class ProfileController extends BaseController
{
    public function index(): Response
    {
        $profile_view_model = new ProfileViewModel($this->container, $this->user_view_model);

        return $this->asView('profile.show', compact('profile_view_model'));
    }

    public function update(AccountService $service): Response
    {
        if (! $this->request->isPatch()) {
            return $this->response->redirect($this->request->callback());
        }

        $profile_updated = $service->updateUserProfile($this->auth->user(), $this->request->validated());

        if (! $profile_updated) {
            $this->flash->error('Profile update failed.');

            return $this->response->redirect($this->request->callback());
        }

        $email_changed = $profile_updated->get('email_changed');

        $this->flash->success('Profile successfully updated.' . ($email_changed ? ' An activation link has been sent to your update email inbox.' : ''));

        return $this->response->redirect($this->request->fullRoute());
    }
}
