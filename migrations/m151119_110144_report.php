<?php

use yii\db\Schema;
use yii\db\Migration;

class m151119_110144_report extends Migration
{
    public function up()
    {
    }

    public function down()
    {
        echo "m151119_110144_report cannot be reverted.\n";

        return false;
    }

    /**
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('reports', [
            'id' => primaryKey(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->text(),
            'class_name' => $this->string(),
            'options' => $this->text(),
            'template' => $this->text(),
        ], $tableOptions);

        $this->createTable('reports_conditions', [
            'id' => primaryKey(),
            'report_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull(),
            'attribute_name' => $this->string()->notNull(),
            'col_label' => $this->string(128),
            'operation' => "enum('select', 'where', 'group', 'order') NOT NULL DEFAULT 'select'",
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . $authManager->ruleTable . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('reports_conditions', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'content' => Schema::TYPE_TEXT,
        ]);
    }

    public function safeDown()
    {
        $this->delete('reports', ['id' => 1]);
        $this->dropTable('news');
    }
}
