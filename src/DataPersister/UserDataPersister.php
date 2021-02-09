<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $userPasswordEncoder,
        private UserRepository $userRepository,
        private ContextAwareDataPersisterInterface $decorated,
        private UserNotifier $userNotifier
    ) {}

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = []): User
    {
        var_dump($data);
        /** @var User $data */
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        /** @var User $result */
        $result = $this->decorated->persist($data, $context);

        if ($result instanceof User && (
            ($context['collection_operation_name'] ?? null) === 'post' ||
            ($context['collection_operation_name'] ?? null) === 'register' ||
            ($context['graphql_operation_name'] ?? null) === 'create'
        )) {
            $this->userNotifier->register($result);
        }

        return $result;
    }

    public function remove($data, array $context = []): User
    {
        return $this->decorated->remove($data, $context);
    }
}
