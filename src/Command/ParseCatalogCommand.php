<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\DataStructures\CatNode;

/**
 * Class ParseCatalogCommand
 * @package App\Command
 */
class ParseCatalogCommand extends Command
{
    protected static $defaultName = 'app:parse-catalog';

    protected function configure()
    {
        $this->setDescription('Parse catalog info from file and build catalog tree')
            ->setHelp('This command allows you to build catalog tree from file...')
            ->addArgument('filename', InputArgument::REQUIRED, 'Path to file with catalog-info');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        if (!is_null($filename)) {
            $file_handle = fopen($filename, "r");
            $catalogTree = new CatNode([0, null, 'Catalog']);
            $dataForCatalog = [];
            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                if (strlen($line)) {
                    $nodeInfo = explode('|', $line);
                    $dataForCatalog[] = $nodeInfo;
                }
            }
            fclose($file_handle);

            uasort($dataForCatalog, function ($x,$y){
                return ($x['1'] > $y['1']);
            });

            foreach ($dataForCatalog as $nodeInfo){
                $catalogTree->addNode(new CatNode($nodeInfo));
            }
            $catalogTree->print($catalogTree,0);

            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
