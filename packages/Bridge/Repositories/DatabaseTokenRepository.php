<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * DatabaseTokenRepository is an implementation of the TokenRepositoryInterface using the database driver.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Repositories;

use Bridge\Contracts\TokenableInterface;
use Bridge\Contracts\TokenRepositoryInterface;
use Bridge\PersonalAccessToken;
use Database\DB;
use DateTimeImmutable;
use Helpers\DateTimeHelper;

class DatabaseTokenRepository implements TokenRepositoryInterface
{
    protected const TABLE = 'personal_access_token';

    public function createToken(TokenableInterface $tokenable, string $name, string $hashedToken, array $abilities = ['*'], DateTimeImmutable|DateTimeHelper|null $expiresAt = null): PersonalAccessToken
    {
        $tokenableType = $tokenable->getTokenableType();
        $tokenableId = $tokenable->getTokenableId();

        $id = DB::table(self::TABLE)->insertGetId([
            'tokenable_type' => $tokenableType,
            'tokenable_id' => $tokenableId,
            'name' => $name,
            'token' => $hashedToken,
            'abilities' => json_encode($abilities),
            'expires_at' => $expiresAt?->format('Y-m-d H:i:s'),
            'created_at' => DateTimeHelper::now()->format('Y-m-d H:i:s'),
        ]);

        return new PersonalAccessToken(id: (int) $id, tokenableType: $tokenableType, tokenableId: $tokenableId, name: $name, hashedToken: $hashedToken, abilities: $abilities, expiresAt: $expiresAt ? DateTimeHelper::parse($expiresAt->format('Y-m-d H:i:s')) : null, createdAt: DateTimeHelper::now());
    }

    public function findToken(int $id): ?PersonalAccessToken
    {
        $row = DB::table(self::TABLE)
            ->where('id', $id)
            ->first();

        if (! $row) {
            return null;
        }

        if (is_array($row)) {
            $row = (object) $row;
        }

        return $this->hydrateToken($row);
    }

    public function deleteToken(int $id): bool
    {
        return DB::table(self::TABLE)
            ->where('id', $id)
            ->delete() > 0;
    }

    public function revokeAllTokens(TokenableInterface $tokenable): int
    {
        return DB::table(self::TABLE)
            ->where('tokenable_type', $tokenable->getTokenableType())
            ->where('tokenable_id', $tokenable->getTokenableId())
            ->delete();
    }

    public function findTokensByTokenable(TokenableInterface $tokenable): array
    {
        $rows = DB::table(self::TABLE)
            ->where('tokenable_type', $tokenable->getTokenableType())
            ->where('tokenable_id', $tokenable->getTokenableId())
            ->get();

        return array_map(fn ($row) => $this->hydrateToken($row), $rows);
    }

    protected function hydrateToken(object|array $row): PersonalAccessToken
    {
        if (is_array($row)) {
            $row = (object) $row;
        }

        return new PersonalAccessToken(
            id: (int) $row->id,
            tokenableType: $row->tokenable_type,
            tokenableId: $row->tokenable_id,
            name: $row->name,
            hashedToken: $row->token,
            abilities: json_decode($row->abilities, true) ?? [],
            expiresAt: $row->expires_at ? DateTimeHelper::parse($row->expires_at) : null,
            createdAt: $row->created_at ? DateTimeHelper::parse($row->created_at) : null
        );
    }
}
