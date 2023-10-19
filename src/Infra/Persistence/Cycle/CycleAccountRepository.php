<?php

namespace RavineRbac\Infra\Persistence\Cycle;

use RavineRbac\Domain\Models\Account;
use RavineRbac\Domain\Models\Enums\AuthTypes;
use RavineRbac\Domain\OptionalApi\Result;
use RavineRbac\Domain\OptionalApi\Result\Err;
use RavineRbac\Domain\OptionalApi\Result\Ok;
use RavineRbac\Domain\Repositories\AccountRepository;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use Cycle\ORM\RepositoryInterface;
use Cycle\Database\Exception\StatementException\ConstrainException;
use RavineRbac\Infra\Persistence\Cycle\Entities\CycleAccount;

class CycleAccountRepository implements AccountRepository
{
    private EntityManager $em;
    public function __construct(private ORM $orm)
    {
        $this->em = new EntityManager($this->orm);
    }

    public function findByAccess(string $access): ?Account
    {
        return $this->repository()->findOne(['access' => $access])?->toModel();
    }

    public function findById(int $id): ?Account
    {
        return $this->repository()->findByPK($id)?->toModel();
    }

    public function findByUUID(string $uuid): ?Account
    {
        $this->repository()->findOne(['uuid' => $uuid])?->toModel();
    }

    public function findWithAuthType(string $access, AuthTypes $authType): ?Account
    {
        $cycleAccount = $this->repository()->findOne(
            [
                'access' => $access,
                'authType' => $authType->value
            ]
        );

        return $cycleAccount?->toModel();
    }

    /** @return Result<bool, \Exception> */
    public function insert(Account $account): Result
    {
        try {
            $this->em->persist(CycleAccount::fromModel($account));

            $this->em->run();

            return new Ok(true);
        } catch (ConstrainException $ex) {
            return new Err($ex);
        } catch (\Exception $ex) {
            return new Err($ex);
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