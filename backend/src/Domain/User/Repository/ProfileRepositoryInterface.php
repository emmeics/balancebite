<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\Profile;
use App\Domain\User\ValueObject\ProfileId;
use App\Domain\User\ValueObject\UserId;

interface ProfileRepositoryInterface
{
    public function save(Profile $profile): void;

    public function findById(ProfileId $id): ?Profile;

    public function findByUserId(UserId $userId): ?Profile;

    public function delete(Profile $profile): void;
}