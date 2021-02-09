<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\UserRegisterInput;
use App\Entity\User;
use App\Service\UserNotifier;

final class UserRegisterInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private ValidatorInterface $validator, private UserNotifier $userNotifier){}

    public function transform($object, string $to, array $context = []): User
    {
        /** @var UserRegisterInput $object */
        $this->validator->validate($object);

        return new User($object->username, $object->email, $object->password, bin2hex(random_bytes(16)));
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return $to === User::class && ($context['input']['class'] ?? null) === UserRegisterInput::class;
    }
}
