<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\MemoryRepositories;

use RavineRbac\Domain\Dto\AccountDto;
use RavineRbac\Domain\Models\Account;
use RavineRbac\Domain\Models\Enums\AuthTypes;
use RavineRbac\Domain\Repositories\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

class InMemoryAccountRepository implements AccountRepository
{
    /**
     * @var Collection<Account>
     */
    public readonly Collection $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection([
            new Account(
                1,
                'mail@mail.com',
                'mailusername',
                'password1',
                'common',
            ),
            new Account(
                2,
                'mail2@mail.com',
                'mailusername2',
                'password2',
                'common',
            ),
            new Account(
                3,
                'mail3@mail.com',
                'mailusername3',
                'password3',
                'common',
            ),
        ]);
    }

    public function findByMail(string $mail): ?Account
    {
        return $this->accounts->findFirst(fn(Account $el) => $el->email === $mail);
    }

    public function findByAccess(string $access): ?Account
    {
        $findBy = filter_var($access, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return $this->accounts->findFirst(fn(Account $el) => $el->$findBy === $access);
    }

    public function findByUUID(string $uuid): ?Account
    {
        return $this->accounts->findFirst(fn(Account $el) => $el->uuid->equals(Uuid::fromString($uuid)));
    }

    public function findWithAuthType(string $email, AuthTypes $authType): ?Account
    {
        return $this->accounts->findFirst(
            fn(Account $el) => $el->email === $email && $el->authType === $authType->value
        );
    }

    /**
     * Inserts a user account.
     *
     * @throws \RavineRbac\Domain\Exceptions\Account\UserAlreadyRegisteredException
     */
    public function insert(AccountDto $account): Account
    {
        $accountInserted = new Account(
            $this->accounts->count() + 1,
            ...$account->getData()
        );
        $this->accounts->add($accountInserted);

        return $accountInserted;
    }
}