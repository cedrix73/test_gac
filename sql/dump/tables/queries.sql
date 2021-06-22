/* 1) Duree totale reelle des appels effectués après le 15/02/2012 inclus */

 SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(appels_emis.duree_reel))) 
 FROM appels_emis 
 INNER JOIN tickets ON appels_emis.id_ticket = tickets.id  
 WHERE tickets.date_ticket >= '2012-02-15' 

 
 /* 2) TOP 10 des volumes data facturés par abonné en dehors de la tranche horaire 08h00-18h00 */
 SELECT tickets.num_abonne, connexions.volume_facture
 FROM connexions 
 INNER JOIN tickets ON connexions.id_ticket = tickets.id 
 AND tickets.heure_ticket NOT BETWEEN '08:00:00' AND '18:00:00' 
 ORDER BY connexions.volume_facture DESC 
 LIMIT 0,10;
 
 
 
 /* 3) Retrouver la quantité totale de SMS envoyés par l'ensemble des abonnés */
 SELECT COUNT(id) 
 FROM tickets 
 WHERE type_donnees LIKE 'envoi de sms%'

