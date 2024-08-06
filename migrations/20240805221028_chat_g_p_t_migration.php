<?php

declare(strict_types=1);

use Phoenix\Database\Element\Index;
use Phoenix\Migration\AbstractMigration;

final class ChatGPTMigration extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('gpt_chat')
            ->addColumn('chat_id', 'biginteger')
            ->addColumn('threads_id', 'string')
            ->addColumn('assistant_id', 'string', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addIndex('threads_id', Index::TYPE_UNIQUE)
            ->addIndex('chat_id', Index::TYPE_UNIQUE)
            ->addForeignKey('chat_id', 'chat', 'id')
            ->create();
    }

    protected function down(): void
    {
        $this->table('gpt_chat')->drop();
    }
}
