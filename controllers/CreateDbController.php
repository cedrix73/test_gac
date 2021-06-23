<?php

include_once 'config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'CreateDbRepository.php';


class createDbController {
    
    private $_dbaccess;
    private $_handler;
    private $_eMessage;
    private $_okMessage;
    private $_createDbRepository; 
    private $_dropSecondaryTables;
    
    public function __construct($dbObj){
        $this->_dbaccess = new DbAccess($dbObj);
        $this->_eMessage = false;
        $this->_okMessage = false;
        $this->_dropSecondaryTables = false;
        try {
            $this->_handler = $this->_dbaccess->connect();
            $this->_createDbRepository = new CreateDbRepository($this->_dbaccess);
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
            echo $this->_handler;
        }
    }



    /**
     * @name         initieTableTicket
     * @description  Création et dump de la table principale tickets
     *               avec un champ date_ticket au bon format sql.
     *               au bon format
     * @return       Bool
     * 
     */
    public function initieTableTicket() {
        $retour = true;
        try {
            $reponse = $this->_createDbRepository->createMainTable();
            if($reponse == false) {
                $retour = false;
                throw new ErrorException("La table principale <i>tickets</i> n'a pu être créée !");
            }
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
        }

        if($retour) {
            $this->_okMessage = "- Table principale <i>tickets</i> déjà peuplée";
            if(!$this->_createDbRepository->isPopulated("tickets")) {
                /***
                 * $_dropSecondaryTable: Ce flag booleen fait supprimer les 2 autres tables puis 
                 * de les recréer afin d'avoir un réalignement des clés secondaires
                 */
                $this->_dropSecondaryTables = true;
                try {
                    $reponse = $this->_createDbRepository->importDumpFile();
                    if($reponse == false) {
                        $retour = false;
                        throw new ErrorException("Erreur lors de l'import des données sur la table <i>tickets</i>");
                    }else{
                        $this->_okMessage = "- Table principale <i>tickets</i> créée et peuplée";
                    }
                } catch (Exception $e) {
                    $this->_eMessage = $e->getMessage();
                }
            }
        }
        return $retour;
    }

    /**
     * @name         initieTableAppels
     * @description  Création et remplissage de la table appels_emis: Cette création 
     *               assure des colonnes duree_volume_reel, duree_volume_facture 
     *               au bon format Time
     * @return       Bool
     * 
     */
    public function initieTableAppels() {
        $retour = true;
        
        
        if ($this->_dropSecondaryTables) {
            try {
                $reponse = $this->_createDbRepository->dropTableAppels();
                if($reponse == false) {
                    $retour = false;
                    throw new ErrorException("La table <i>appels_emis</i> n'a pu être supprimée !");
                }
            } catch (Exception $e) {
                $this->_eMessage = $e->getMessage();
            }
        }
        try {
            $reponse = $this->_createDbRepository->createTableAppelsEmis();
            if($reponse == false) {
                $retour = false;
                throw new ErrorException("La table <i>appels_emis</i> n'a pu être créée !");
            }
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
        }

        if($retour) {
            $this->_okMessage = "- Table principale <i>appels_emis</i> déjà peuplée";
            if(!$this->_createDbRepository->isPopulated("appels_emis")) {
                try {
                    $reponse = $this->_createDbRepository->populateAppelsEmis();
                    if($reponse == false) {
                        $retour = false;
                        throw new ErrorException("Erreur lors de l'import des données sur la table <i>appels_emis</i>");
                    }else{
                        $this->_okMessage = "- Table <i>appels_emis</i> créée et peuplée";
                    }
                } catch (Exception $e) {
                    $this->_eMessage = $e->getMessage();
                }
            }
        }
        return $retour;
    }


    /**
     * @name         initieTableConnexions
     * @description  Création et remplissage de la table connexion. : Cette création 
     *               assure des colonnes duree_volume_reel, duree_volume_facture 
     *               au bon format Numeric
     * @return       Bool
     * 
     */
    public function initieTableConnexions() {
        $retour = true;
        if ($this->_dropSecondaryTables) {
            try {
                $reponse = $this->_createDbRepository->dropTableConnexions();
                if($reponse == false) {
                    $retour = false;
                    throw new ErrorException("La table <i>connexions</i> n'a pu être supprimée !");
                }
            } catch (Exception $e) {
                $this->_eMessage = $e->getMessage();
            }
        }
        try {
            $reponse = $this->_createDbRepository->createTableConnexions();
            if($reponse == false) {
                $retour = false;
                throw new ErrorException("La table <i>connexions</i> n'a pu être créée !");
            }
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
        }

        if($retour) {
            $this->_okMessage = "- Table principale <i>connexions</i> déjà peuplée";
            if(!$this->_createDbRepository->isPopulated("connexions")) {
                try {
                    $reponse = $this->_createDbRepository->populateConnexions();
                    if($reponse == false) {
                        $retour = false;
                        throw new ErrorException("Erreur lors de l'import des données sur la table <i>connexions</i>");
                    }else{
                        $this->_okMessage = "- Table <i>connexions</i> créée et peuplée";
                    }
                } catch (Exception $e) {
                    $this->_eMessage = $e->getMessage();
                }
            }
        }
        return $retour;
    }


    public function getErrorMessage() {
        return $this->_eMessage;
    }

    public function getOkMessage() {
        return $this->_okMessage;
    }




}


?>
