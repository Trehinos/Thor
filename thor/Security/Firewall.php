<?php

namespace Thor\Security;

use JetBrains\PhpStorm\Immutable;

class Firewall
{

    public function __construct(
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public string $pattern = '/',
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $redirect = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $loginRoute = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $logoutRoute = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public ?string $checkRoute = null,
        #[Immutable(allowedWriteScope: Immutable::PROTECTED_WRITE_SCOPE)]
        public array $exclude = []
    ) {
    }

}
