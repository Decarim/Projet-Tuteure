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

class BoissonModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllBoisson(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id_boisson','type_boisson')
            ->from('boisson')
            ->orderBy('id_boisson');
        return $queryBuilder->execute()->fetchAll();
    }

    public function getBoisson($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('b.id_boisson', 'b.type_boisson')
            ->from('boisson', 'b')
            ->where('b.id_boisson='.$id);
        return $queryBuilder->execute()->fetch();

    }
    public function insertBoisson($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('boisson')
            ->values(['type_boisson' => '?'])
            ->setParameter(0,$donnees['type_boisson']);
        return $queryBuilder->execute();
    }
    public function updateBoisson($donnees)
    {
        var_dump($donnees);
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->update('boisson')
            ->set('type_boisson' , '?')
            ->where("id_boisson = ".$donnees['id_boisson'])
            ->setParameter(0, $donnees['type_boisson'])

        ;
        return $queryBuilder->execute();
    }

    public function getLibelle($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('m.libelle_menu')
            ->from('menu','m')
            ->innerJoin('m','boisson','b','b.id_boisson = m.id_boisson')
            ->where('b.id_boisson='.$id);
        return $queryBuilder->execute()->fetchAll();
    }

    public function deleteBoisson($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('boisson')
            ->where('id_boisson='.$donnees['id_boisson']);
        return $queryBuilder->execute();
    }

    public function autoCompleteBoisson()
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('type_boisson')
            ->from('boisson');
        $result = $queryBuilder->execute()->fetchAll();
        $tab = array_map('current', $result);
        return $tab;
    }

    public function getId($boisson)
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->select('id_boisson')
            ->from('boisson')
            ->where('type_boisson = ?')
            ->setParameter('0',$boisson);
        return $queryBuilder->execute()->fetch();
    }
}
