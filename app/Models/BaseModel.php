<?php


namespace App\Models;

use Interop\Container\ContainerInterface;

class BaseModel
{
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct(ContainerInterface $ci)
    {
        $this->pdo = $ci->get('pdo');
    }
}
