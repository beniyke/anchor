<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ClientManagerService handles business logic for client entities,
 * including creation, status management, and relationship lookups.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Services;

use Client\Enums\ClientStatus;
use Client\Exceptions\ClientException;
use Client\Models\Client;
use Client\Notifications\StatusChangeNotification;
use Client\Notifications\WelcomeEmailNotification;
use Client\Services\Builders\ClientBuilder;
use Database\Exceptions\ValidationException;
use Helpers\Data;
use Helpers\Validation\Validator;
use Mail\Mail;

class ClientManagerService
{
    public function make(): ClientBuilder
    {
        return new ClientBuilder();
    }

    /** @throws ValidationException */
    public function create(array $data): Client
    {
        $validator = (new Validator())->rules([
            'name' => ['required' => true, 'type' => 'string', 'maxlength' => 255],
            'email' => ['required' => true, 'type' => 'email', 'unique' => 'client.email'],
            'phone' => ['required' => false, 'type' => 'string', 'maxlength' => 20],
        ])->validate($data);

        if ($validator->has_error()) {
            throw new ValidationException("Client validation failed.", $validator->errors());
        }

        if (! isset($data['status'])) {
            $data['status'] = ClientStatus::Pending;
        }

        $client = Client::create($data);

        if ($client->email) {
            Mail::send(new WelcomeEmailNotification(Data::make([
                'name' => $client->name,
                'email' => $client->email,
                'portal_url' => config('client.urls.portal_login', 'client/login'),
            ])));
        }

        return $client;
    }

    public function update(int|string $id, array $data): Client
    {
        $client = Client::find($id);

        if (! $client) {
            throw new ClientException("Client with ID {$id} not found.");
        }

        $client->update($data);

        return $client;
    }

    public function activate(int|string $id): bool
    {
        $client = Client::find($id);

        if (! $client) {
            return false;
        }

        $updated = $client->update(['status' => ClientStatus::Active]);

        if ($updated && $client->email) {
            Mail::send(new StatusChangeNotification(Data::make([
                'name' => $client->name,
                'email' => $client->email,
                'status_label' => ClientStatus::Active->label(),
                'alert_type' => ClientStatus::Active->alertType(),
                'account_url' => config('client.urls.account', 'client/account'),
            ])));
        }

        return $updated;
    }

    public function suspend(int|string $id): bool
    {
        $client = Client::find($id);

        if (! $client) {
            return false;
        }

        $updated = $client->update(['status' => ClientStatus::Suspended]);

        if ($updated && $client->email) {
            Mail::send(new StatusChangeNotification(Data::make([
                'name' => $client->name,
                'email' => $client->email,
                'status_label' => ClientStatus::Suspended->label(),
                'alert_type' => ClientStatus::Suspended->alertType(),
                'account_url' => config('client.urls.account', 'client/account'),
            ])));
        }

        return $updated;
    }

    public function findByEmail(string $email): ?Client
    {
        return Client::query()->where('email', $email)->first();
    }

    public function findByRefid(string $refid): ?Client
    {
        return Client::query()->where('refid', $refid)->first();
    }

    public function getByReseller(int|string $resellerId): array
    {
        return Client::query()->where('owner_id', (int) $resellerId)->get()->all();
    }
}
