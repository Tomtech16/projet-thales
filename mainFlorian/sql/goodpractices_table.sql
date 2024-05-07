--
-- Structure de la table GOODPRACTICES
--

CREATE TABLE checklist.GOODPRACTICES (
  goodpracticeskey INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  program VARCHAR(100) NOT NULL,
  phase CHAR(12) NOT NULL,
  item VARCHAR(300) NOT NULL,
  keywords VARCHAR(100)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


--
-- Contenu de la table GOODPRACTICES
--

INSERT INTO GOODPRACTICES (program, phase, item, keywords) VALUES
('PROG_1, PROG_2', 'codage', 'creertest <test>avec option -c ou -p selon besoin' ,'TOUS'),
('PROG_1, PROG_2', 'codage', 'modini <test>', 'TOUS'),
('PROG_1, PROG_2', 'codage', 'Vérifier que les dates dans le .tp correspond au fichier définition dans REF_CONTEXT/<contexte>/definition', NULL),
('PROG_1, PROG_2', 'codage', 'Mettre une instruction set_1553_Error pour chaque instrument PL utilisé : Set_1553_Error ("1", "TIMEOUT"); /* POSEIDON 1 = 1, POSEIDON 2 = 4 */ Set_1553_Error ("7", "TIMEOUT"); /* DORIS 1 = 7, DORIS 2 = 10 */', 'PL, POS3'),
('PROG_1, PROG_2', 'codage', "Autoriser l'utilisation de la FDTM durant le test", 'TOUS, sauf deploiement'),
('PROG_1, PROG_2', 'codage', 'Modifier la configuration du RM en cas de reconfiguration inattendue durant le test', 'TOUS, MeO'),
('PROG_1, PROG_2', 'codage', "Envoyer les TC spécifiques de reprise de contexte des SADM spécifiques avant l'envoi des TC decontexte", 'post RDP1'),
('PROG_1, PROG_2', 'codage', "Modifier la FDIR pour le health status GYRO pendant le mode TEST après l'envoi des TC de contexte", 'annulé'),
('PROG_1, PROG_2', 'codage', 'La procedure de WarmStart à utiliser se trouve dans la librairie cc_proc_warmstart. Elle porte le nom WS_miss5_<ctx_reprise>_PM<A ou B>(<tGPSWarmStart>).', 'MeO, GPS, warmstart'),
('PROG_1, PROG_2', 'exécution', 'Vérifier que outputs et outputs_ctx sont accessibles en écriture', 'TOUS'),
('PROG_1, PROG_2', 'exécution', 'Vérifier la date de la dernière calibration. Elle doit être < 15 jours sinon, il faut calibrer les gyros', 'BF GYRO, calibration'),
('PROG_1, PROG_2', 'exécution', 'Mettre à jour le fichier gyrostd.cal dans $SGSE_HOME/CURRENT_CONF/CHR et $SGSE_HOME/CONF/CHR en fonction des gyros utilisés', 'BF GYRO'),
('PROG_2', 'exécution', 'Modifier le fichier plbs.chr pour configurer le paquet DIODE sur le RT, la sous-adresse, la fréquence souhaitée (si paquet diode nécessaire)', 'PL'),
('PROG_2', 'exécution', "Connecter l'EBB POS-3 en fonction du PM utilisé (si besoin)", 'PL, POS3'),
('PROG_1, PROG_2', 'exécution', "Vérifier que l'alimentation du DHU est ON", 'TOUS'),
('PROG_2', 'exécution', "Mettre ON les alimentations de l'EBB POS-3 quand nécessaire", 'PL, POS3'),
('PROG_1, PROG_2', 'analyse', 'ana_fonc <test> -c -v', 'TOUS'),
('GENERIQUE', 'préparation', 'réception équipement: liste des documents à demander', 'TOUS'),
('GENERIQUE', 'préparation', 'expédition équipement: liste des documents à fournir', 'TOUS');