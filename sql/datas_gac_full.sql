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


/*  Creation et remplissage table appels emis */
CREATE TABLE IF NOT EXISTS appels_emis(
  id_ticket integer NOT NULL,
  duree_reel time,
  duree_facture time,
  type_donnees varchar(50) NOT NULL,
  PRIMARY KEY (id_ticket)
);


INSERT INTO appels_emis (id_ticket, duree_reel, duree_facture, type_donnees)
SELECT id, duree_volume_reel, duree_volume_facture, type_donnees
FROM tickets
WHERE (type_donnees LIKE 'appel vers %' 
 OR type_donnees LIKE  'appel de %' 
 OR type_donnees LIKE  'appel émis %' 
 OR type_donnees LIKE  'appel vocal %'
 OR type_donnees LIKE  'appels internes %');
 
 
 /*  Creation et remplissage table connexions */
 CREATE TABLE IF NOT EXISTS connexions (
  id_ticket integer NOT NULL,
  volume_reel DECIMAL(10,2),
  volume_facture DECIMAL(10,2),
  type_donnees varchar(50) NOT NULL,
  PRIMARY KEY (id_ticket)
);


INSERT INTO connexions (id_ticket, volume_reel, volume_facture, type_donnees)
SELECT id, duree_volume_reel, duree_volume_facture, type_donnees
FROM tickets
WHERE type_donnees LIKE 'connexion%'; 