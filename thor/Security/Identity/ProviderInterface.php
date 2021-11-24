<?php

namespace Thor\Security\Identity;

interface ProviderInterface
{

    public function getIdentity(string $identifier): ?IdentityInterface;

}
