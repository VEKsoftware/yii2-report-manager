<?php

use yii\db\Schema;
use reportmanager\migrations\Migration;

class m151119_110144_init extends Migration
{
    public function safeUp()
    {
        $this->createTable('reports', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->text(),
            'class_name' => $this->string(255)->notNull(),
            'creator_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'options' => $this->text()->notNull(),
            'template' => $this->text()->notNull(),
        ], $this->tableOptions);

        $this->createTable('reports_conditions', [
            'id' => $this->primaryKey(),
            'report_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull(),
            'attribute_name' => $this->string()->notNull(),
            'col_label' => $this->string(128)->notNull(),
            'operation' => "ENUM('select', 'where', 'group', 'order') NOT NULL DEFAULT 'select'",
            'function' => $this->string(255)->notNull(),
            'param' => $this->text(),
            'plan' => $this->string(255)->defaultValue('NULL'),
        ], $this->tableOptions);

        $this->addForeignKey('rep_conds_fk', 'reports_conditions', 'report_id', 'reports', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('reports_conditions');
        $this->dropTable('reports');
    }
}