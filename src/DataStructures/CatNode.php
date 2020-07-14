<?php
namespace App\DataStructures;
/**
 * Class CatNode
 * @package App\DataStructures
 */
class CatNode
{

    protected $id;
    protected $parent_id;
    protected $title;
    protected $children;

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
     * @param \App\Command\CatNode $node
     */
    public function addNode(CatNode $node)
    {
        if ($this->id == $node->parent_id) {
            $this->children[] = $node;
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

        foreach ($pNode->children as $childrenNode) {
            if ($childrenNode->getId() == $nodeId) {
                $node = $childrenNode;
            } else {
                if (is_array($childrenNode->getchildren()) && count($childrenNode->getchildren())) {
                    $node = $this->findNodeById($childrenNode, $nodeId);
                }
            }
        }
        return $node;
    }

    /**
     * @param CatNode $tree
     * @param $currentLevel
     */
    public function print(CatNode $tree, $currentLevel)
    {
        $prefix = '';
        $prefix .= str_repeat('-', $currentLevel);
        echo $prefix . $tree->getTitle() . "\n";
        if (is_array($tree->getchildren())) {
            foreach ($tree->getchildren() as $childrenNode) {
                $this->print($childrenNode, $currentLevel+1);
            }
        }
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
        $this->children[] = $node;
    }

    /**
     * @return mixed
     */
    public function getchildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
