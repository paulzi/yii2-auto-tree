<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests\models;

use paulzi\adjacencyList\AdjacencyListBehavior;
use paulzi\materializedPath\MaterializedPathBehavior;
use paulzi\autotree\AutoTreeTrait;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $sort
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $path
 * @property string $slug
 *
 * @property NodeAlMp[] $parents
 * @property NodeAlMp $parent
 * @property NodeAlMp $root
 * @property NodeAlMp[] $descendants
 * @property NodeAlMp[] $children
 * @property NodeAlMp[] $leaves
 * @property NodeAlMp $prev
 * @property NodeAlMp $next
 *
 * @method static NodeAlMp|null findOne() findOne($condition)
 *
 * @mixin AdjacencyListBehavior
 * @mixin MaterializedPathBehavior
 */
class NodeAlMp extends \yii\db\ActiveRecord
{
    use AutoTreeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tree}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'         => AdjacencyListBehavior::className(),
                'sortable'      => false,
            ],
            [
                'class'         => MaterializedPathBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @return NodeQuery
     */
    public static function find()
    {
        return new NodeQuery(get_called_class());
    }
}