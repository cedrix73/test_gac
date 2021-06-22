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