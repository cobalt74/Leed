/* #############
###  MISE A JOUR Base de donnée de Leed pour fonctionnement avec la version Multi Utilisateur

Conseils : 
- Avant d'effectuer la mise à jour, sauvegarder votre BDD et exporté vos flux en OPML
- Attention : "leed_" est à remplacer par votre prefix de table
- ce fichier est a supprimer après installation.

Description :
- utilisé pour un passage de la version mono a multi utilisateur.
- Les requêtes suivantes sont a executer sur votre Base de données Leed avec phpMyAdmin par exemple


ATTENTION, si votre prefix a été changé lors de votre première installation, il faut le remplacer.
############### */

-- Mise à jour table User pour le prefix des Tables en fonction des utilisateurs
ALTER TABLE `leed_user` ADD `prefixDatabase` VARCHAR(255) NOT NULL;

-- mise à jour de l'utilisateur admin - affectation du prefix de base.
UPDATE `leed_user` set `prefixDatabase` = 'leed_' where `id` = 1;