<?php

namespace Thor\Security;

use Exception;
use Thor\Database\PdoRowInterface;
use Thor\Database\PdoRowTrait;

interface UserInterface
{

    public function getUsername(): string;

    public function getPwdHash(): string;


}
