<?php

// src/Command/ParseNewsCommand.php

namespace App\Command;

use App\Service\NewsParserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;


#[AsCommand(name: 'parse-news')]
class ParseNewsCommand extends Command
{
    private $newsParserService;

    public function __construct(NewsParserService $newsParserService)
    {
        $this->newsParserService = $newsParserService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Download and parse news articles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->newsParserService->parseNews();
        $output->writeln('News parsing complete!');
        return Command::SUCCESS;
    }
}