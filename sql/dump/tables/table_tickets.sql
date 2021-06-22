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