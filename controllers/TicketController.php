<?php

include_once 'config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'TicketRepository.php';


class TicketController {
    
    private $_dbaccess;
    private $_handler;
    private $_eMessage;
    private $_ticketRepository;
    
    public function __construct($dbObj){
        $this->_dbaccess = new DbAccess($dbObj);
        $this->_eMessage = false;
        try {
            $this->_handler = $this->_dbaccess->connect();
            $this->_ticketRepository = new TicketRepository($this->_dbaccess);
        } catch (Exception $e) {
            echo $this->_handler;
        }
        
    }


    /**
     * @name         getDureeTotalAppels
     * @description  retourne le résultat de TicketRepository::getTotalLenghtSentCall()
     * @return       Integer $retour 
     * @return       Bool false
     * 
     */
    public function getDureeTotalAppels() {
        $retour = false;
        try {
            $reponse = $this->_ticketRepository->getTotalLenghtSentCall();
            $results = $this->_dbaccess->fetchAssoc($reponse);
            $retour = $results['duree_totale'];
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
        }
        
        ////$retour = json_encode($tabServices);
        return $retour;
    }


    /**
     * @name         getHighestDataFactures
     * @description  retourne le résultat de TicketRepository::getTopDatasFactures()
     * @return       Array $retour['num_abonne'],['volume_facture']
     * @return       Bool false
     * 
     */
    public function getHighestDataFactures() {
        $retour = false;
        try {
            $retour = $this->_ticketRepository->getTopDatasFactures();
            $control = $this->_dbaccess->fetchArray($retour);
            $controlFormat = true;
            $tabRetour = array();
            $i = 0;
            foreach($control as $value) {
                $tabRetour[$i]['num_abonne'] = intval($value['num_abonne']);
                $tabRetour[$i]['volume_facture'] = floatval($value['volume_facture']);
                $controlFormat = is_int( $tabRetour[$i]['num_abonne']) ? true: false;
                $controlFormat = is_float($tabRetour[$i]['volume_facture']) ? true : false;
                $i++;
            }
            if($controlFormat === false) {
                throw new ErrorException('Le format des données est invalide !');
            }
            $retour=$tabRetour; 
            unset($control); 
               
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
        }
        
        ////$retour = json_encode($tabServices);
        return $retour;
    }

    /**
     * @name         quantiteTotaleSms
     * @description  retourne le résultat de TicketRepository::getAllSms()
     * @return       Integer $retour 
     * @return       Bool false
     * 
     */
    public function quantiteTotaleSms() {
        $retour = false;
        try {
            $reponse = $this->_ticketRepository->getAllSms();
            $results = $this->_dbaccess->fetchAssoc($reponse);
            $retour = $results['nb_sms_tot'];
        } catch (Exception $e) {
            $this->_eMessage = $e->getMessage();
        }
        
        ////$retour = json_encode($tabServices);
        return $retour;
    }



    public function fermerConnection() {
        if(isset($this->_handler)) {
            $this->_dbaccess->close($this->_handler);
        }

    }

    public function getErrorMessage() {
        return $this->_eMessage;
    }


}


?>
