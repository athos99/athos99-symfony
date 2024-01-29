<?php

namespace App\Logger;

use App\Security\User;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Bundle\SecurityBundle\Security;


/**
 * Add login name in logger
 */
#[AsMonologProcessor]
class UserProcessor
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

// this method is called for each log record; optimize it to not hurt performance
    public function __invoke(LogRecord $record)
    {
        try {
            /** @var User $user */
            $user = $this->security->getUser();
            $record->extra['user'] = $user?->getUserIdentifier();
        } catch (\Throwable $e) {
            return $record;
        }

        return $record;
    }
}
