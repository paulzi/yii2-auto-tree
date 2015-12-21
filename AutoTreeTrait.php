<?php

namespace paulzi\autotree;

use yii\base\InvalidCallException;

trait AutoTreeTrait
{
    /**
     * @var \yii\base\Behavior[]
     */
    private $_autoTreeMap;

    private $_autoTreeReturnBehavior = false;


    /**
     * @return array
     */
    private static function autoTreeAliases()
    {
        return [
            'paulzi\adjacencyList\AdjacencyListBehavior'       => 'al',
            'paulzi\adjacencylist\AdjacencyListBehavior'       => 'al',
            'paulzi\nestedSets\NestedSetsBehavior'             => 'ns',
            'paulzi\nestedsets\NestedSetsBehavior'             => 'ns',
            'paulzi\nestedIntervals\NestedIntervalsBehavior'   => 'ni',
            'paulzi\nestedintervals\NestedIntervalsBehavior'   => 'ni',
            'paulzi\materializedPath\MaterializedPathBehavior' => 'mp',
            'paulzi\materializedpath\MaterializedPathBehavior' => 'mp',
        ];
    }

    /**
     *
     */
    private function autoTreeInit()
    {
        /** @var \yii\base\Component|self $this */
        $aliases = $this->autoTreeAliases();
        foreach ($this->getBehaviors() as $behavior) {
            $className = $behavior::className();
            if (isset($aliases[$className]) && !isset($this->_autoTreeMap[$aliases[$className]])) {
                $this->_autoTreeMap[$aliases[$className]] = $behavior;
            }
        }
    }

    /**
     * @param string $method
     * @param array $list
     * @param array $arguments
     * @param bool $firstOnly
     * @return mixed
     */
    private function autoTreeCall($method, $list, $arguments = [], $firstOnly = true)
    {
        if ($this->_autoTreeMap === null) {
            $this->autoTreeInit();
        }

        $result  = null;
        $founded = false;
        foreach ($list as $alias) {
            if (isset($this->_autoTreeMap[$alias])) {
                $behavior = $this->_autoTreeMap[$alias];
                if (method_exists($behavior, $method)) {
                    if ($this->_autoTreeReturnBehavior) {
                        return $behavior;
                    }
                    $founded = true;
                    $result = call_user_func_array([$behavior, $method], $arguments);
                    if ($firstOnly) {
                        return $result;
                    }
                }
            }
        }

        if (!$founded) {
            throw new InvalidCallException("Method '{$method}' not founded");
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        /** @var \yii\db\BaseActiveRecord|self $this */
        // replace getParents() by getParentsOrdered() if behavior not support ordered query
        if ($name === 'parents') {
            $this->_autoTreeReturnBehavior = true;
            $behavior = $this->getParents();
            $this->_autoTreeReturnBehavior = false;
            if (method_exists($behavior, 'getParentsOrdered')) {
                $this->populateRelation($name, $behavior->getParentsOrdered());
            }
        }
        if ($name === 'descendants') {
            $this->_autoTreeReturnBehavior = true;
            $behavior = $this->getDescendants();
            $this->_autoTreeReturnBehavior = false;
            if (method_exists($behavior, 'getDescendantsOrdered')) {
                $this->populateRelation($name, $behavior->getDescendantsOrdered());
            }
        }
        return parent::__get($name);
    }

    /**
     * @param int|null $depth
     * @return \yii\db\ActiveQuery
     */
    public function getParents($depth = null)
    {
        return $this->autoTreeCall('getParents', ['mp', 'ns', 'ni', 'al'], [$depth]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->autoTreeCall('getParent', ['al', 'mp', 'ns', 'ni']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoot()
    {
        return $this->autoTreeCall('getRoot', ['mp', 'ns', 'ni', 'al']);
    }

    /**
     * @param int|null $depth
     * @param bool $andSelf
     * @return \yii\db\ActiveQuery
     */
    public function getDescendants($depth = null, $andSelf = false)
    {
        return $this->autoTreeCall('getDescendants', ['ns', 'ni', 'mp', 'al'], [$depth, $andSelf]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->autoTreeCall('getChildren', ['al', 'ns', 'ni', 'mp']);
    }

    /**
     * @param int|null $depth
     * @return \yii\db\ActiveQuery
     */
    public function getLeaves($depth = null)
    {
        return $this->autoTreeCall('getLeaves', ['ns', 'al', 'mp', 'ni'], [$depth]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\NotSupportedException
     */
    public function getPrev()
    {
        return $this->autoTreeCall('getPrev', ['ns', 'mp', 'ni', 'al']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\NotSupportedException
     */
    public function getNext()
    {
        return $this->autoTreeCall('getNext', ['ns', 'mp', 'ni', 'al']);
    }

    /**
     * Populate children relations for self and all descendants
     * @param int $depth = null
     * @return self
     */
    public function populateTree($depth = null)
    {
        return $this->autoTreeCall('populateTree', ['ns', 'ni', 'mp', 'al'], [$depth]);
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->autoTreeCall('isRoot', ['al', 'ns', 'ni', 'mp']);
    }

    /**
     * @param \yii\db\BaseActiveRecord $node
     * @return bool
     */
    public function isChildOf($node)
    {
        return $this->autoTreeCall('isChildOf', ['ns', 'ni', 'mp', 'al'], [$node]);
    }

    /**
     * @return bool
     */
    public function isLeaf()
    {
        return $this->autoTreeCall('isLeaf', ['ns', 'al', 'ni', 'mp']);
    }

    /**
     * @return $this
     */
    public function makeRoot()
    {
        return $this->autoTreeCall('makeRoot', ['al', 'ns', 'ni', 'mp'], [], false);
    }

    /**
     * @param \yii\db\BaseActiveRecord $node
     * @return $this
     */
    public function prependTo($node)
    {
        return $this->autoTreeCall('prependTo', ['al', 'ns', 'ni', 'mp'], [$node], false);
    }

    /**
     * @param \yii\db\BaseActiveRecord $node
     * @return $this
     */
    public function appendTo($node)
    {
        return $this->autoTreeCall('appendTo', ['al', 'ns', 'ni', 'mp'], [$node], false);
    }

    /**
     * @param \yii\db\BaseActiveRecord $node
     * @return $this
     */
    public function insertBefore($node)
    {
        return $this->autoTreeCall('insertBefore', ['al', 'ns', 'ni', 'mp'], [$node], false);
    }

    /**
     * @param \yii\db\BaseActiveRecord $node
     * @return $this
     */
    public function insertAfter($node)
    {
        return $this->autoTreeCall('insertAfter', ['al', 'ns', 'ni', 'mp'], [$node], false);
    }

    /**
     * @return bool|int
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function deleteWithChildren()
    {
        return $this->autoTreeCall('deleteWithChildren', ['ns', 'ni', 'mp', 'ai']);
    }
}