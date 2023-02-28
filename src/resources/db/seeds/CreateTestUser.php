<?php


use Phinx\Seed\AbstractSeed;

class CreateTestUser extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $posts = $this->table('users');
        $posts->insert([
            'email' => 'geovannylc@gmail.com',
            'password' => password_hash('321321', PASSWORD_BCRYPT),
            'created' => date(DATE_W3C),
        ])->saveData();
    }
}
