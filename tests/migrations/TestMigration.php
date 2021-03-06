<?php
/**
 * @link https://github.com/paulzi/yii2-auto-tree
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-auto-tree/blob/master/LICENSE)
 */

namespace paulzi\autotree\tests\migrations;

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class TestMigration extends Migration
{
    public function up()
    {
        ob_start();
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if ($this->db->getTableSchema('{{%tree}}', true) !== null) {
            $this->dropTable('{{%tree}}');
        }
        $this->createTable('{{%tree}}', [
            'id'        => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' NULL',
            'sort'      => Schema::TYPE_INTEGER . ' NULL',
            'tree'      => Schema::TYPE_INTEGER . ' NULL',
            'lft'       => Schema::TYPE_INTEGER . ' NULL',
            'rgt'       => Schema::TYPE_INTEGER . ' NULL',
            'depth'     => Schema::TYPE_INTEGER . ' NULL',
            'path'      => Schema::TYPE_STRING . ' NULL',
            'slug'      => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);
        $this->createIndex('parent_sort', '{{%tree}}', ['parent_id', 'sort']);
        $this->createIndex('lft', '{{%tree}}', ['tree', 'lft', 'rgt']);
        $this->createIndex('rgt', '{{%tree}}', ['tree', 'rgt']);
        $this->createIndex('path', '{{%tree}}', ['tree', 'path']);

        // update cache (sqlite bug)
        $this->db->getSchema()->getTableSchema('{{%tree}}', true);
        ob_end_clean();
    }
}
