--
-- Base de données :  `gac_tickets`
--
CREATE DATABASE IF NOT EXISTS `gac_tickets` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `gac_tickets`;


/*  Creation et remplissage table générique tickets depuis dump csv */

CREATE TABLE IF NOT EXISTS tickets (
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
);


LOAD DATA INFILE './dump/tickets_appels_201202.csv' 
IGNORE 
INTO TABLE tickets
FIELDS TERMINATED BY ';' 
OPTIONALLY ENCLOSED BY "'"
LINES TERMINATED BY '\r\n'
(compte,num_facture, num_abonne,@date_time_variable,heure_ticket,duree_volume_reel,duree_volume_facture,type_donnees)
SET date_ticket = STR_TO_DATE(@date_time_variable, '%d/%m/%Y');