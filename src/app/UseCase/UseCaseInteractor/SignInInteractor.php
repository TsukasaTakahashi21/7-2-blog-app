<?php

namespace App\UseCase\UseCaseInteractor;
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Adapter\QueryServise\UserQueryServise;
use App\UseCase\UseCaseInput\SignInInput;
use App\UseCase\UseCaseOutput\SignInOutput;
use App\Domain\Entity\User;
use App\Domain\ValueObject\HashedPassword;

final class SignInInteractor
{
  const FAILED_MESSAGE = 'メールアドレスまたは<br/>パスワードが間違っています';

  const SUCCESS_MESSaGE = 'ログインしました';
  
  private $userQueryService;
  private $input;

  public function __construct(SignInInput $input)
  {
    $user = $this->userQueryService = new UserQueryServise();
    $this->input = $input;
  }

  public function handler(): SignInOutput
  {
    $user = $this->findUser();

    if ($this->notExistUser($user)) {
      return new SignInOutput(false, self::FAILED_MESSAGE);
    }

    if($this->isInvalidPassword($user->password())) {
      return new SignInOutput(false, self::FAILED_MESSAGE);
    }

    $this->savaSession($user);
    return new SignInOutput(true, self::SUCCESS_MESSaGE);
  }

  private function findUser(): ?User
  {
    return $this->userQueryService->findByEmail($this->input->email());
  }

  private function notExistUser(?User $user): bool{
    return is_null($user);
  }

  private function isInvalidPassword(HashedPassword $hashedPassword): bool{
    return !$hashedPassword->verify($this->input->password());
  }

  private function savaSession(User $user): void
  {
    $_SESSION['user']['id'] = $user->id()->value();
    $_SESSION['user']['name'] = $user->name()->value();
  }
}

?>