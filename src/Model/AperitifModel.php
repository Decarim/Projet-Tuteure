<?php
/**
 * Created by PhpStorm.
 * User: Ciryion
 * Date: 09/11/2017
 * Time: 18:50
 */
namespace App\Model;
use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class AperitifModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllAperitif(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id_aperitif','libelle_aperitif')
            ->from('aperitif')
            ->orderBy('id_aperitif');
        return $queryBuilder->execute()->fetchAll();
    }
}