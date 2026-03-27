<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Dto\Request\TokenLoginRequest;
use App\Auth\Dto\Response\LoginResult;

interface TokenLoginServiceInterface
{
    public function login(TokenLoginRequest $request): LoginResult;
}
