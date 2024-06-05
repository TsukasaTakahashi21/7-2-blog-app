<?php
namespace App\UseCase\UseCaseInteractor;
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Adapter\QueryServise\UserQueryServise;
use App\Adapter\Repository\UserRepository;
use App\UseCase\UseCaseInput\SignUpInput;
use App\UseCase\UseCaseOutput\SignUpOutput;
use App\Domain\ValueObject\User\NewUser;
use App\Domain\Entity\User;

final class SignUpInteractor
{
    const ALLREADY_EXISTS_MESSAGE = 'すでに登録済みのメールアドレスです';

    const COMPLETED_MESSAGE = '登録が完了しました';

    private $userRepository;

    private $userQueryServise;

    private $input;

    public function __construct(SignUpInput $input)
    {
        $this->userRepository = new UserRepository();
        $this->userQueryServise = new UserQueryServise();
        $this->input = $input;
    }

    public function handler(): SignUpOutput
    {
        $user = $this->findUser();

        if ($this->existsUser($user)) {
            return new SignUpOutput(false, self::ALLREADY_EXISTS_MESSAGE);
        }

        $this->signup();
        return new SignUpOutput(true, self::COMPLETED_MESSAGE);
    }

    private function findUser(): ?User
    {
        return $this->userQueryServise->findByEmail($this->input->email());
    }

    private function existsUser(?User $user): bool
    {
        return !is_null($user);
    }

    private function signup(): void
    {
        $this->userRepository->insert(
            new NewUser(
                $this->input->name(),
                $this->input->email(),
                $this->input->password()
            )
        );
    }
}
