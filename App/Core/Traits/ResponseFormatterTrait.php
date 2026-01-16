<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Helpers\Format\FormatCollection as Format;
use Helpers\Http\Response;

trait ResponseFormatterTrait
{
    /**
     * Returns a JSON response.
     */
    protected function asJson(array $data, int $code = 200): Response
    {
        return $this->response->status($code)
            ->header(['Content-Type' => 'application/json'])
            ->body(Format::asJson($data));
    }

    /**
     * Returns an XML response.
     */
    protected function asXml(array $data, int $code = 200): Response
    {
        return $this->response->status($code)
            ->header(['Content-Type' => 'application/xml'])
            ->body(Format::asXml($data));
    }

    /**
     * Returns a standard unauthorized (401) JSON response.
     */
    protected function unauthorized(): Response
    {
        return $this->asJson(
            Format::asFailedApiResponse('Unauthorized Access'),
            401
        );
    }

    /**
     * Returns a standardized API response (success or failure).
     */
    public function asApiResponse(bool $is_successful, string $message = 'Request completed successfully', mixed $data = null, ?array $metadata = null, int $status_code = 200): Response
    {
        if ($is_successful) {
            return $this->asJson(Format::asSuccessfulApiResponse($message, $data, $metadata), $status_code);
        }

        return $this->asJson(Format::asFailedApiResponse($message), $status_code);
    }
}
