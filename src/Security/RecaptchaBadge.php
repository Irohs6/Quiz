<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class RecaptchaBadge implements BadgeInterface
{
    private string $recaptchaResponse;
    private bool $resolved;

    public function __construct(string $recaptchaResponse)
    {
        $this->recaptchaResponse = $recaptchaResponse;
        $this->resolved = false;
    }

    public function getRecaptchaResponse(): string
    {
        return $this->recaptchaResponse;
    }

    public function markResolved(): void
    {
        $this->resolved = true;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }
}