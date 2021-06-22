<?php

/**
 * @name MySqli
 * @author cvonfelten
 * Classe gérant le driver mysqli et interface les méthodes de DBInterface
 */
require_once ABS_CLASSES_PATH.'DbInterface.php';


class DbMySqli implements DbInterface {

    private $_noMsg;
    private $_stmt;


    public function setLog($bln) {
        $this->_noMsg = $bln;
    }

    public function getLog() {
        return $this->_noMsg;
    }

	/* Etablit une connexion à un serveur de base de données et retourne un identifiant de connexion
	   L'identifiant est positif en cas de succès, FALSE sinon.
	   On pourrait se connecter avec un utilisateur lambda
	   */
	public function connect($conInfos, $no_msg = 0)
	{
        $this->_noMsg = $no_msg;
        $this->_stmt = false;
        $link =  false;
        $host = $conInfos['host'];
		$dbname = $conInfos['dbase'];
        $dbh = new mysqli($host = $host, $conInfos['username'], $conInfos['password'], $dbname);
		if ($dbh->connect_error)
		{
            if ($no_msg == false){
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                echo "Erreur de connexion N° ". mysqli_connect_errno();
            }
            $this->close($dbh);
		} else {
            $link = $dbh;
        }
		
		return $link;
	}
	
	

	/* Ferme la connexion au serveur MySQL associée à l'identifiant $hcon
	   Retourne TRUE en cas de succès, FALSE sinon */
	public function close($link) {
		return mysqli_close($link);
	}

	/**
         * Sélectionne la base de données $db
         * --> Sans objet pour mysqli. 
         */
        
	public function select_db($link, $dbName) {
            return $link->select_db($this->link, $dbName);
    }
    
    /**
	 * @name: execQuery
	 * @description: Execute la requete SQL $query et renvoie  le resultSet
	 * pour être interprétée ultérieurement par fetchRow ou fetchArray.
	 * 
	 * @param ressource $link: instance renvoiée lors de la connexion PDO.
	 * @param string $query: chaine SQL
	 * @return array $resultSet : resultat de l'execution
	 */
	public function execQuery($link, $query) {
        $resultSet = $link->real_query($query);
        $resultSet = $link->store_result();
        if (!$resultSet) {
            if($this->_noMsg !== false) {
                printf("Erreur : %s\n", mysqli_error($link));
            }
            
        }
		return $resultSet;
	}

	/**
	 * @name: execPreparedQuery
	 * @description: il s'agit d'un prpared Statement: Prépare et execute 
	 * la requete SQL $query et renvoie  le resultSet pour être interprétée 
	 * ultérieurement par fetchRow ou fetchArray. Si on passe des arguments 
	 * dans la fonction, ils doivent être passés dans le tableau clé-valeur 
	 * $args avec comme format de clé (combinaisons possibles:):
     * i - Integer 
     * d - Double
     * s - String
     * b - Blob
     * correspondant aux '?' de la requête SQL.
     * ":nomDeLaVariable" => valeurDeLaVariable.
     * Utilisée pour traiter bcp de resultats, la MYSQLI_USE_RESULT
     * est utilisée: ne pas oublier de libérer la requete par
     * $link->free_result($result) après le fetchRow ou fetchArray.
     * 
     * @param ressource $link: instance renvoiée lors de la connexion PDO.
	 * @param string $query: chaine SQL
	 * @param boolean $again: Si true, le même statement est réexecuté avec de
	 *                de nouveaux arguments; $query peut être vide.
	 * @return mixed $stmt : retourne le statement de la requête.
     */
	public function execPreparedQuery($link, $query, $args=null, $again) {
        if(!$again) {
			$this->_stmt = false;
		}
        try {   
            if($again || $this->_stmt = $link->prepare($query)){
                if($args !== null) {
                    foreach ($args as $varName => $varValue) {
                        $this->_stmt->bindParam($varName, $varValue);
                    }
                }
                $this->_stmt->execute();
            } 

        } catch (mysqli_sql_exception $e) {
            if($this->_noMsg !== false) {
                printf("Erreur : %s\n", mysqli_error($link));
            }
            $link->free_result();
        }
        return $this->_stmt;
	}

    /**
	 * @name:          numRows
	 * @description:   Retourne le nombre de lignes qui sera retournées ultérieurement par
	 *                 fetchRow ou fetchArray.
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function numRows($resultSet) {
		return $resultSet->affected_rows();
	}

	/**
	 * @name:          fetchRow
	 * @description:   Retourne un tableau énuméré clé-valeur  dont les indexes de clé sont numériques 
	 *                 et correspondent dans l'ordre des colonnes spécifiées en clause SELECT.
	 *                 Retourne FALSE s'il n'existe pas de résultat.
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function fetchRow($resultSet) 
	{
        $results = false;
        $tabResults = array();
        $tabResults = $resultSet->fetch_all(MYSQLI_NUM);
        $resultSet->free_result();
        //$resultSet->close();
		return $tabResults;
	}
	
	/**
	 * @name:          fetchArray
	 * @description:   Retourne un tableau associatif dont la clé correspond aux nom colonnes 
	 *                 spécifiées en clause SELECT. Retourne FALSE s'il n'existe pas de résultat. 
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function fetchArray($resultSet) 
	{
		$results = false;
        $tabResults = array();
        $tabResults = $resultSet->fetch_all(MYSQLI_ASSOC);
        $resultSet->free_result();
        //$resultSet->close();
		return $tabResults;
	}


    /**
	 * @name:          fetchAssoc
	 * @description:   Retourne une ligne de résultat sous forme de tableau associatif 
	 *                 dont la clé correspond aux nom colonnes spécifiées en clause SELECT. 
	 * 			       Retourne FALSE s'il n'existe pas de résultat. 
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function fetchAssoc($resultSet) 
	{
		$results = false;
		try {
			$results = $resultSet->fetch(MYSQLI_ASSOC);
		} catch (PDOException $e) {
			if($this->_noMsg !== false) {
				echo 'Problème lors du traitement du résultat de la requête ' 
			   . ' en tableau associatif: ' . $e->getMessage();
			   $result = false;
			}
			
		}
		return $results;
	}
	
	public function escapeString($link, $arg){
		return $link->real_escape_string($arg);
	}
        
    public function multipleQueries($link, $queries){
        $tabResults = array();
        if ($this->link->multi_query($link, $queries)) {
            do {
                /* Stockage du premier jeu de résultats */
                if ($result = mysqli_store_result($link)) {
                    while ($row = mysqli_fetch_row($result)) {
                        $tabResults[] = $row;
                    }
                    $this->link->free_result();
                }
                /* Affichage d'une démarcation */
                if ($this->link->more_results($this->link)) {
                    //printf("-----------------\n");
                }
            } while ($this->link->next_result($this->link));
        }
        return $tabResults;
    }
        
}

?>
