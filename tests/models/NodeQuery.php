<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests\models;

use paulzi\adjacencylist\AdjacencyListQueryTrait;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class NodeQuery extends \yii\db\ActiveQuery
{
    use AdjacencyListQueryTrait;
}