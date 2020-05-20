<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'admin:create';

    /** @var UserPasswordEncoderInterface */
    protected $encoder;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(UserPasswordEncoderInterface $encoder, ContainerInterface $container)
    {
        parent::__construct();

        $this->encoder = $encoder;
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create first admin')
            ->addArgument('password', null, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $password = $input->getArgument('password');

        $user = new User();
        $user->setRoles([ 'ROLE_USER', 'ROLE_ADMIN' ]);
        $user->setUsername('admin');
        $password = $this->encoder->encodePassword($user, $password);
        $user->setPassword($password);

        $doctrine = $this->container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $entityManager->persist($user);
        $entityManager->flush();

        $io->success('User successfully created');

        return 0;
    }
}
