<?php

namespace RavineRbac\Domain\Exceptions\Account;

class UserAlreadyRegisteredException
{
    private string $responsaMessage = 'O nome de usuário ou o email escolhido já foi utilizado';
}
