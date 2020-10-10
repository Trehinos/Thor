<?php

namespace Packages\EMS\Main\Forms;

use Thor\Html\Form\Form;
use Thor\Html\Form\PasswordType;
use Thor\Html\Form\TextType;
use Thor\Http\Request;

final class ChangePassword extends Form
{

    public function __construct(string $action, string $method = Request::POST)
    {
        parent::__construct($action, $method);
    }

    /**
     * @return array
     */
    public static function formDefinition(): array
    {
        return [
            new PasswordType('new-password', true),
            new PasswordType('password-confirm', true),
        ];
    }
}
