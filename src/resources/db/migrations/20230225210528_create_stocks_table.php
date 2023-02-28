<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStocksTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $users = $this->table('stocks', ['id' => true]);
        $users->addColumn('name', 'string')
            ->addColumn('close', 'float')
            ->addColumn('symbol', 'string')
            ->addColumn('open', 'float')
            ->addColumn('high', 'float')
            ->addColumn('low', 'float')
            ->addColumn('date', 'datetime')
            ->create();
    }
}
