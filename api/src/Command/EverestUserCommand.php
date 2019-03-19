<?php

declare(strict_types=1);

/*
 * This file is part of Everest Monitoring.
 *
 * (c) Simon Reitinger
 *
 * @license LGPL-3.0-or-later
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EverestUserCommand extends Command
{
    protected static $defaultName = 'everest:user';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var bool
     *           defines if at least one property was changed or updated
     */
    private $changed = false;

    /**
     * EverestUserCommand constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidatorInterface           $validator
     */
    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordEncoderInterface $passwordEncoder,
                                ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a User')
            ->addArgument('username', InputArgument::OPTIONAL, 'Checks for existing users. 
                If the user does not exist, the name is used to create a new user.')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'option to delete the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $delete = $input->getOption('delete');

        $choice = '';

        if ($username) {
            // try to get an existing user
            $user = $this->entityManager->getRepository(User::class)->findOneByUsername($username);

            if ($user) {
                $io->text(sprintf('User %s found', $username));

                if ($delete) {
                    if ($io->confirm(sprintf('Do you really want to delete %s?', $username), false)) {
                        $this->entityManager->remove($user);
                        $this->entityManager->flush();
                        $io->success('User deleted.');

                        return;
                    }
                }

                $this->updateUser($user, $io);
                if ($this->changed) {
                    $io->success('User updated.');
                } else {
                    $io->success('Done. Nothing udpated.');
                }

                return;
            }

            $io->text(sprintf('Creating a new user with name %s', $username));
            $user = $this->createUser($io, $username);

            return;
        }

        $user = $this->createUser($io);
    }

    /**
     * @param SymfonyStyle $io
     * @param string       $username
     *
     * @return User
     */
    private function createUser(SymfonyStyle $io, $username = '')
    {
        $user = new User();

        if ($username) {
            $user->setUsername($username);
            $choices = $this->getChoices(false, false);
        } else {
            $choices = $this->getChoices(false);
        }

        foreach ($choices as $choice) {
            $this->performChoiceOnUser($choice, $user, $io);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User %s created!', $user->getUsername()));

        return $user;
    }

    /**
     * @param User         $user
     * @param SymfonyStyle $io
     *
     * @throws \ReflectionException
     */
    private function updateUser(User $user, SymfonyStyle $io): void
    {
        $choices = $this->getChoices(true);

        do {
            $choice = $io->choice('Select the field to be updated. Press enter to save & exit', $choices, 'save');

            if ($choice === 'save') {
                break;
            }

            $this->performChoiceOnUser($choice, $user, $io);
        } while ($choice !== '');

        if ($this->changed) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    private function performChoiceOnUser($choice, User $user, SymfonyStyle $io): void
    {
        switch ($choice) {
            case 'save':
                // leave the switch and the loop
                return;

            case 'password':
                if ($user->getPassword()) {
                    $oldPassword = $io->askHidden('Enter the current password');

                    if (!$this->passwordEncoder->isPasswordValid($user, $oldPassword)) {
                        $io->error('You entered a wrong password');
                        break;
                    }
                }

                $value = $this->passwordEncoder->encodePassword($user, $io->askHidden('Set the new password'));
                $user->setPassword($value);
                $this->changed = true;
                break;
            case 'email':
                $value = $io->ask(sprintf('Enter the %s', $choice));

                $constraint = new EmailConstraint();
                $errors = $this->validator->validate($value, $constraint);

                if ($errors->count()) {
                    $io->error($errors);
                } else {
                    $user->setEmail($value);
                }
                $this->changed = true;
                break;
            default:
                $value = $io->ask(sprintf('Enter the %s', $choice));
                $user->{'set'.ucfirst($choice)}($value);
                $this->changed = true;
                break;
        }
    }

    /**
     * @param bool  $saveAndExit
     * @param mixed $username
     *
     * @return array
     */
    private function getChoices($saveAndExit = false, $username = true): array
    {
        $choices = [];

        if ($saveAndExit) {
            $choices[] = 'save';
        }

        if ($username) {
            $choices[] = 'username';
        }
        $choices[] = 'password';
        $choices[] = 'email';
        $choices[] = 'firstName';
        $choices[] = 'lastName';

        return $choices;
    }
}
