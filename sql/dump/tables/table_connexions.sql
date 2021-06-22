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