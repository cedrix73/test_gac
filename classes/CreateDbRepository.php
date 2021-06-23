<?php


/**
 * CreateDbRepository
 * Classe gérant la création des tables ticket, appels_emis, connexions 
 * ainsi que l'importation des données partir du fichier csv fourni dans sql/dump
 * CRUD
 * @author Cédric Von Felten
 */
class CreateDbRepository {
    
    private $_dbaccess;
    private $_sql;
    
    public function __construct($dbaccess){
        $this->_dbaccess = $dbaccess;
        $this->_sql = '';
    }
    
    public function getSql(){
        return $this->_sql;
    }

        /**
     * @name        createDataBase
     * @description Crée la base de données gac_tickets
     * @return boolean  $reponse
     */
    public function createDatabase() {
        $this->_sql = " CREATE DATABASE IF NOT EXISTS 'gac_tickets' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci; USE 'gac_tickets';";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }
    
    /**
     * @name        createMainTable
     * @description Crée la table principale tickets
     * @return boolean  $reponse
     */
    public function createMainTable() {
        $this->_sql = " CREATE TABLE IF NOT EXISTS tickets (
                        id integer NOT NULL AUTO_INCREMENT,
                        compte integer NOT NULL,
                        num_facture integer NOT NULL, 
                        num_abonne integer NOT NULL,
                        date_ticket date NOT NULL,
                        heure_ticket time NOT NULL,
                        duree_volume_reel varchar(10),
                        duree_volume_facture varchar(10),
                        type_donnees varchar(50) NOT NULL,
                        PRIMARY KEY (id)
                    );";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }
    
    /**
     * @name        importDumpFile
     * @description Remplit la table principale tickets à partir du fichier csv
     * @return boolean  $reponse
     */
    
    public function importDumpFile() {
        // Modifie le charactère de séparation des sous-répertoires DOS en "/"
        $rightPath = str_replace("\\", "/", ABS_DUMP_PATH);

        $this->_sql = " LOAD DATA  INFILE '". $rightPath . "/tickets_appels_201202.csv' 
                        IGNORE 
                        INTO TABLE tickets
                        FIELDS TERMINATED BY ';' 
                        OPTIONALLY ENCLOSED BY '\\\"'
                        LINES TERMINATED BY '\\n'
                        (compte,num_facture, num_abonne,@date_time_variable,heure_ticket,duree_volume_reel,duree_volume_facture,type_donnees)
                        SET date_ticket = STR_TO_DATE(@date_time_variable, '%d/%m/%Y');)
                        );"; 
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }

    
    /**
     * @name        createTableAppelsEmis
     * @description Crée la table appels emis
     * @return boolean  $reponse
     */
    public function createTableAppelsEmis() {
        $this->_sql = " CREATE TABLE IF NOT EXISTS appels_emis(
            id_ticket integer NOT NULL,
            duree_reel time,
            duree_facture time,
            type_donnees varchar(50) NOT NULL, 
            CONSTRAINT appels_fk FOREIGN KEY (id_ticket)
            REFERENCES tickets(id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
            PRIMARY KEY (id_ticket)
            );";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }


    /**
     * @name            populateAppelsEmis
     * @description     Remplis la table appels emis à partir de la table tickets et
     *                  rien que les appels émis.
     * @return boolean  $reponse
     */
    public function populateAppelsEmis() {
        $this->_sql = " INSERT INTO appels_emis (id_ticket, duree_reel, duree_facture, type_donnees)
                        SELECT id, duree_volume_reel, duree_volume_facture, type_donnees
                        FROM tickets
                        WHERE (type_donnees LIKE 'appel vers %' 
                            OR type_donnees LIKE  'appel de %' 
                            OR type_donnees LIKE  'appel émis %' 
                            OR type_donnees LIKE  'appel vocal %'
                            OR type_donnees LIKE  'appels internes %'
                        );";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }

    /**
     * @name        createTableConnexions
     * @description Créé la table connexions (données 3G, 3G+..) et permet 
     *              ainsi de formater les volumes reels et factures par le bon 
     *              type de données (DECIMAL) qui seront lisibles en float.
     * @return boolean  $reponse
     */
    public function createTableConnexions() {
        $this->_sql = " CREATE TABLE IF NOT EXISTS connexions (
                        id_ticket integer NOT NULL,
                        volume_reel DECIMAL(10,2),
                        volume_facture DECIMAL(10,2),
                        type_donnees varchar(50) NOT NULL,
                        CONSTRAINT connexions_fk FOREIGN KEY (id_ticket)
                        REFERENCES tickets(id)
                        ON UPDATE CASCADE
                        ON DELETE CASCADE,
                        PRIMARY KEY (id_ticket)
                        );";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }


    /**
     * @name          populateConnexions
     * @description   Remplis la table connexions à partir des données de 
     *                connexion la table tickets.
     * @return boolean  $reponse
     */
    public function populateConnexions() {
        $this->_sql = " INSERT INTO connexions (id_ticket, volume_reel, volume_facture, type_donnees)
                        SELECT id, duree_volume_reel, duree_volume_facture, type_donnees
                        FROM tickets
                        WHERE type_donnees LIKE 'connexion%'; 
                        );";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }

    

    /**
     * @name          isPopulated
     * @description   Vérifie si la table $nomTable a bien été peuplée
     * @param  string $nomTable
     * @return bool    $nbColonnes
     */
    public function isPopulated($nomTable) {
        $retour = false;
        $this->_sql = " SELECT COUNT(*) AS nb_lignes" .
                      " FROM " . $nomTable  .
                      " LIMIT 0,5 ";
        $rs = $this->_dbaccess->execQuery($this->_sql);
        $tabResult = $this->_dbaccess->fetchAssoc($rs);
        $retour = ($tabResult['nb_lignes'] > 0) ? true : false;
        return $retour;
    }

    /**
     * @name         dropTableAppels 
     * @description  Supprime la table appels_emis
     * @return bool  $reponse
     */
    public function dropTableAppels() {
        $this->_sql = " DROP TABLE appels_emis; ";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }


    /**
     * @name         dropTableConnexions 
     * @description  Supprime la table connexions
     * @return bool  $reponse
     */
    public function dropTableConnexions() {
        $this->_sql = " DROP TABLE connexions; ";
        $reponse = $this->_dbaccess->execQuery($this->_sql);
        return $reponse;
    }


    
    
    
}

?>