<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CatNode
 * @package App\Command
 */
class CatNode
{

    protected $id;
    protected $parent_id;
    protected $title;
    protected $childrens;

    public static $currentLevel;

    /**
     * CatNode constructor.
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        if (!is_null($data)) {
            $this->id = (int)$data[0];
            $this->parent_id = (int)$data[1];
            $this->title = trim(preg_replace('/\s+/', ' ', $data[2]));
        }
    }

    /**
     * @param CatNode $node
     */
    public function addNode(CatNode $node)
    {
        if ($this->id == $node->parent_id) {
            $this->childrens[] = $node;
        } else {
            $parent = $this->findNodeById($this, $node->parent_id);
            if (!is_null($parent)) {
                $parent->addChildren($node);
            }
        }
    }

    /**
     * @param CatNode $pNode
     * @param $nodeId
     * @return mixed|null
     */
    public function findNodeById(CatNode $pNode, $nodeId)
    {
        $node = null;

        foreach ($pNode->childrens as $childrenNode) {
            if ($childrenNode->getId() == $nodeId) {
                $node = $childrenNode;
            } else {
                if (is_array($childrenNode->getChildrens()) && count($childrenNode->getChildrens())) {
                    $node = $this->findNodeById($childrenNode, $nodeId);
                }
            }
        }
        return $node;
    }

    /**
     * @param CatNode $tree
     */
    public function print(CatNode $tree)
    {
        $prefix = '';
        $prefix .= str_repeat('-', CatNode::$currentLevel);
        echo $prefix . $tree->getTitle() . "\n";
        if (is_array($tree->getChildrens())) {
            foreach ($tree->getChildrens() as $childrenNode) {
                CatNode::$currentLevel++;
                $this->print($childrenNode);
            }
        }
        CatNode::$currentLevel--;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param CatNode $node
     */
    public function addChildren(CatNode $node)
    {
        $this->childrens[] = $node;
    }

    /**
     * @return mixed
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}

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

            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                if (strlen($line)) {
                    $nodeInfo = explode('|', $line);
                    $catalogTree->addNode(new CatNode($nodeInfo));
                }
            }
            fclose($file_handle);

            CatNode::$currentLevel = 0;
            $catalogTree->print($catalogTree);

            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
