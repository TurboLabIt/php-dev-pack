<?php
namespace TurboLabIt\TLIBaseBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


abstract class BaseCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'BaseCommand';

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $io;

    protected $startedAt;
    protected $em;


    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% -- %message%');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input        = $input;
        $this->output       = $output;
        $this->io           = new SymfonyStyle($input, $output);
        $this->startedAt    = new \DateTime();

        if (!$this->lock()) {

            $this->io->error('The command ##' . $this->getName() . '## is already running in another process.');
            return self::FAILURE;
        }

        $this->io->block("Running ##" . $this->getName() . "## - Start: " . $this->startedAt->format("Y-m-d H:m:s"), null, 'fg=black;bg=cyan', ' ', true);

        return self::SUCCESS;
    }


    protected function success()
    {
        $this->io->success('Success! - End: ' . (new \DateTime())->format("Y-m-d H:i:s"));
        return self::SUCCESS;
    }
}
