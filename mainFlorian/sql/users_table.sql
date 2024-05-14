--
-- Structure de la table USERS
--

DROP TABLE IF EXISTS USERS;

CREATE TABLE USERS (
  user_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  username VARCHAR(30) NOT NULL, 
  firstname VARCHAR(100) NOT NULL,
  lastname VARCHAR(100) NOT NULL, 
  `profile` CHAR(10) NOT NULL, 
  `password` CHAR(60) NOT NULL, 
  attempts INT NOT NULL, 
  UNIQUE (username)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


--
-- Contenu de la table USERS
--

INSERT INTO USERS (user_id, username, firstname, lastname, profile, password, attempts) VALUES
(1, 'operator', 'thales', 'thales', 'operator', '$2y$10$dGtIzyANyT6PuMWSOkuc9.7uBNENbKwXCXNq9V33BYuwwK/nQ78kG', 0),
(2, 'admin', 'thales', 'thales', 'admin', '$2y$10$dGtIzyANyT6PuMWSOkuc9.7uBNENbKwXCXNq9V33BYuwwK/nQ78kG', 0),
(3, 'superadmin', 'thales', 'thales', 'superadmin', '$2y$10$dGtIzyANyT6PuMWSOkuc9.7uBNENbKwXCXNq9V33BYuwwK/nQ78kG', 0);

--- Mots de passe : "password"