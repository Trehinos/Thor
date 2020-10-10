<?php

namespace Thor\Security;

interface UserInterface
{

    public function getUsername(): string;

    public function hasPwdHashFor(string $clearPassword): bool;

    public function getRoles(): array;

}
