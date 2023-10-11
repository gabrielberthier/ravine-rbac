<?php

namespace RavineRbac\Infra\Persistence\Cycle;

use RavineRbac\Data\Entities\Cycle\CycleAccount;
use RavineRbac\Domain\Dto\AccountDto;
use RavineRbac\Domain\Exceptions\Account\UserAlreadyRegisteredException;
use RavineRbac\Domain\Models\Account;
use RavineRbac\Domain\Models\Enums\AuthTypes;
use RavineRbac\Domain\Repositories\AccountRepository;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use Cycle\ORM\RepositoryInterface;
use Cycle\Database\Exception\StatementException\ConstrainException;

class CycleAccountRepository implements AccountRepository
{
    private EntityManager $em;
    public function __construct(private ORM $orm)
    {
        $this->em = new EntityManager($this->orm);
    }

    public function findByAccess(string $access): ?Account
    {
        $findBy = filter_var($access, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $cycleAccount = $this->repository()->findOne([$findBy => $access]);

        return $cycleAccount?->toModel();
    }

    public function findByMail(string $mail): ?Account
    {
        $cycleAccount = $this->repository()->findOne(['email' => $mail]);

        return $cycleAccount?->toModel();
    }

    public function findByUUID(string $uuid): ?Account
    {
        $cycleAccount = $this->repository()->findOne(['uuid' => $uuid]);

        return $cycleAccount?->toModel();
    }

    public function findWithAuthType(string $email, AuthTypes $authType): ?Account
    {
        $cycleAccount = $this->repository()->findOne(
            [
                'email' => $email,
                'authType' => $authType->value
            ]
        );

        return $cycleAccount?->toModel();
    }

    public function insert(AccountDto $accountDto): Account
    {
        try {
            $account = new CycleAccount();
            $account->setEmail($accountDto->email)
                ->setUsername($accountDto->username)
                ->setAuthType($accountDto->authType->value)
                ->setPassword($accountDto->password);
            $this->em->persist($account);

            $this->em->run();

            return $account?->toModel();
        } catch (ConstrainException) {
            throw new UserAlreadyRegisteredException();
        }
    }

    /**
     * @return RepositoryInterface<CycleAccount>
     */
    private function repository(): RepositoryInterface
    {
        return $this->orm->getRepository(CycleAccount::class);
    }
}