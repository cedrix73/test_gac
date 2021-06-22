<?php


/**
 * TicketRepository
 * Classe gérant les requêtes pour les appels, les connexions data et les sms
 * CRUD
 * @author CV170C7N
 */
class TicketRepository {
    
    private $dbaccess;
    private $_sql;
    
    public function __construct($dbaccess){
        $this->dbaccess = $dbaccess;
        $this->_sql = '';
    }
    
    public function getSql(){
        return $this->_sql;
    }
    
    /**
     * @name        getDureeTotaleReelle
     * @description Duree totale reelle des appels effectués après le 15/02/2012 inclus 
     * @return int  $results['duree_totale']
     */
    public function getDureeTotaleReelle() {
        $this->_sql = " SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(appels_emis.duree_reel))) AS duree_totale " .
                      " FROM appels_emis " .
                      " INNER JOIN tickets ON appels_emis.id_ticket = tickets.id " .
                      " WHERE tickets.date_ticket >= '2012-02-15' ";
        $reponse = $this->dbaccess->execPreparedQuery($this->_sql);
        $results=$this->dbaccess->fetchAssoc($reponse);
        
        return $results['duree_totale'];
    }
    



    /**
     * @name         getTopDatasFactures
     * @description  Retourne le TOP 10 des volumes data facturés 
     *               par abonné en dehors de la tranche horaire 08h00-18h00 
     * @return Array $results      
     */
    public function getTopDatasFactures(){
        $this->_sql = " SELECT tickets.num_abonne, connexions.volume_facture " .
                      " FROM connexions " .
                      "  INNER JOIN tickets ON connexions.id_ticket = tickets.id " .
                      " AND tickets.heure_ticket NOT BETWEEN '08:00:00' AND '18:00:00'  " .
                      " ORDER BY connexions.volume_facture DESC  " .
                      " LIMIT 0,10 ";
        $reponse = $this->dbaccess->execQuery($this->_sql);
        $results=$this->dbaccess->fetchArray($reponse);
        return $results;
    }


    /**
     * @name          quantiteTotaleSms
     * @description   Retourne la quantité totale de SMS envoyés 
     *                par l'ensemble des abonnés
     *  @return int  $results['nb_sms_tot']
     */
    public function quantiteTotaleSms() {
        $this->_sql = " SELECT COUNT(id) AS nb_sms_tot " .
                      " FROM tickets  " .
                      " WHERE type_donnees LIKE 'envoi de sms%' ";
        $reponse = $this->dbaccess->execPreparedQuery($this->_sql);
        $results=$this->dbaccess->fetchAssoc($reponse);
 
        return $results['nb_sms_tot'];
    }
    

 
    
    
}
