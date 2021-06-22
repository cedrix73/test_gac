<?php

include_once realpath(dirname(__FILE__)).'/../config.php';

function checkcheck($post_group, $val_element, $type = 'checkbox'){
    if(!is_array($post_group)){
            if(isset($_POST[$post_group]) && $_POST[$post_group] == $val_element){
                    switch($type){
                            case 'radio':
                                    echo ' checked = "checked"';
                            break;
                            case 'selected':
                                    echo ' selected';
                            break;
                            case 'checkbox':
                                    echo ' checked = true';
                            break;
                    }
            }
    }else{
        foreach($_POST[$post_group] as $key => $value){
                if($value == $val_element){
                        echo ' checked = "checked"';
                }
        }
    }	
}


function rightEncode($string) {
    mb_detect_encoding($string) != 'UTF-8' ?: $string = utf8_encode($string);
    return $string;

}



/**
 * @name tabLoad
 * @description  Crée un tableau à partir d'une table avec une structure telle que:
 *               <nomTable>: nomTable_id, nom du champ libellé
 * @param String  $nomChampSql
 * @param String  $nomTableBd
 * @param pointer $db
 * @return type
 */
function tabLoad($nomChampSql, $nomTableBd, $db, $filtre = null){
    $nom_table = $nomTableBd;
    $tab = array();
    $sql = 'SELECT DISTINCT (id), '.$nomChampSql . 
            ' FROM '.$nomTableBd;
    if($filtre != null){
       $sql .= ' '.$filtre; 
    }
    $reponse = $db->execQuery($sql);
    $results=$db->fetchRow($reponse);
    foreach ($results as $value) {
        $id = $value[0];
        $libelle = rightEncode($value[1]);
        $tab[$id] = $libelle;
    }
    return $tab;
}

/**
 * @description Suite logique d'un tabLoad si on veut charger les résultats dans 
 * une combo avec une valeur sélectionnée par défaut 
 */

function getOptionsFromTab($tab, $selected = null) 
{
    $options = false;
    if (is_array($tab) && count($tab) > 0) {
        $trouve = false;
        foreach ($tab as $key => $value) {
            $options .= '<option id = ' . $key 
                        . ' value="'. $key.'"';
            if ($selected != null || !$trouve) {
                $pref = ($key == $selected) ? ' selected = selected ' : '';
                $options .= ' ' . $pref;
            }
            $options .= '>' . rightEncode($value) . '</option>';
        }
    }
    return $options;
}


/**
 * @name        selectLoad() 
 * @description Charge dans une combobox les valeurs des colonnes
 * $nomChampSql de la table $nomTableBd par un tableau clé (id) - 
 * valeur ($nomChampSql ) à partir d'une requete sql en un One Shot
 * 
 * @param string $nomChampSql nom du champ à afficher pour l'option
 * @param string $nomTableBd  nom de la table contenant nomChampSql
 * @param type $db          instance de la classe-repository DbAccess
 * @param string $filtre    valeur sélectionnée par défaut si corrélation
 * @return string
 */
function selectLoad($nomChampSql, $nomTableBd, $db, $filtre = null)
{
    $nom_table = $nomTableBd;
    $tab = array();
    $sql = 'SELECT DISTINCT (id), '.$nomChampSql . 
            ' FROM '.$nomTableBd;
    $reponse = $db->execQuery($sql);
    $results = $db->fetchArray($reponse);
    $options = '';
    if (is_array($results) && count($results) > 0) {
        $trouve = false;
        foreach ($results as $value) {
            $options .= '<option id = ' . $value['id'] 
                      . ' value="'. $value['id'] .'"';
            if ($filtre !== null || !$trouve) {
                $pref = ($value['id'] == $filtre) ? ' selected = selected ' : '';
                $options .= ' ' . $pref;
            }
            $options .= '>' . rightEncode($value[$nomChampSql]);
            $options .= '</option>';
        }
    }
    return $options;
}




/**
 * listeLoad
 * 
 * @param type $nomChampSql
 * @param type $nomTable
 * @param type $db
 * @return array
 */
function listeLoad($nomChampSql, $nomTable, $db, $filtre = null){
    $liste = array();
    $sql = 'SELECT DISTINCT(' . $nomChampSql . ') FROM '.$nomTable;
    if($filtre != null){
       $sql .= ' '.$filtre; 
    }
    $reponse = $db->execQuery($sql);
    array_push($liste, "Tous *");
    $results = $db->fetchRow($reponse);
    if (is_array($results) && count($results) > 0) {
        foreach ($results as $ligne) {
            array_push($liste, $ligne[0]);
        }
    }
    
    return $liste;
}


?>