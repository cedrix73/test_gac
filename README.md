# test_gac
Il s'agit d'une micro application de test pour une évaluation technique de chez GAC.
Elle permet l'insertion d'un fichier de données csv. Ces données sont stockées dans la table principale tickets.
Or ce fichier contient dans deux colonnes un mélange de données de type numeric ou time.

CHARGEMENT DES DONNEES ET TABLES
La création d'une classe cvcparser avait été initialement envisagée pour modifier le typage de ces données et leur insertion en une seule passe.
Toutefois, en raison d'un temps de chargement jugé trop long et d'un memory_limit de 128M dans sa configuration standard, 
la solution d'une requête LOAD DATA  INFILE a été privilégiée. Le seul défaut: la cohérence des dates n'est pas vérifiée.

2 autres tables sont ensuite crées: appels_emis et connexions
=> 'appels_emis': dédiée aux requêtes concernant les appels: champs duree_reel  et duree_facture au format time
=> 'connexions':  dédiée aux requêtes concernant les connexions: champs volume_reel et volume_facture en format decimal(10,2)

NB: Concernant la requête pour les appels, l'énoncé précise 'appels effectués'. En jouant de précaution avec cette ambiguïté
 (appels émis par le propriétaire du compte), la table appels_emis charge donc tous les appels émis ('appel vers %', 'appel de%',
 'appel vocal %', 'appels internes %') et en excluant donc les renvois et les appels reçus.
 (cf: CreateDbRepository::populateAppelsEmis)

Concernant les SMS, il n'y avait pas besoin de créer une table dédiée car les champs champs duree_reel  et duree_facture sont vides.

STRUCTURE
L'application n'utilise ni frameworks, ni grosses bibliothèque bien encombrante et inutile. Il fallait rester simple. Il y a donc un 
seul fichier d'index comme page de démarrage qui joue également le rôle de template. La seule bibliothèque que j'ai réutilisé sont 
la classes d'abstraction de base de données et les classes dédiées à mysql ou mysqli.

Il y a deux fichiers controlleurs (CreateDbController et TicketController) qui pilotent les requêtes des classes repository 
(TicketRepository et CreateDbRepository) dédiée aux requêtes.
=> CreateDbController: S'occupe de la création et du remplissage des tables (partie 1)
=> TicketController: S'occupe du déroulement de l'exploitation des données (partie 2)

Pour le css (pas de scss ou de sass): une bibliothèque telle que Bulma, légère, fait l'affaire.

COMMENT DEMARRER ?
Il faut tout d'abord avoir les accès priviégiés en base de données.
Modifier ensuite dans le fichier config.php le nom d'utilisateur et le mot de passee en modifiant ces 2 lignes 
avec ceux de votre accès en privilège:

define('GAC_DATABASE_USER' , 'root');
define('GAC_DATABASE_PASSWORD', 'cedrix');

Il est évidemment conseillé de modifier l'utilisateur 'root' avec un utilisateur avec privilèges en back-office mysql.

Avec ces accès, créer la base de données 'gac_tickets':
	CREATE DATABASE IF NOT EXISTS 'gac_tickets' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
	USE `gac_tickets`;

EVOLUTIONS POSSIBLES
Il s'agit ici d'un Minimal Viable Product. A une prochaine itération, les évolutions suivantes pourraient 
être envisagées:
Implémentation de fonctions javascript pour faire des requêtes ajax vers les controlleurs avec:
- un loader pour la création des tables
- un feedback sous forme de message dès la table crée ou remplie
- l'execution des queries par des événements utilisateurs (bouton)