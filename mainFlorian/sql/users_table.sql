--
-- Structure de la table USERS
--

CREATE TABLE checklist.USERS (
  userkey INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
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

INSERT INTO USERS (username, firstname, lastname, profile, password, attempts) VALUES
('operator', 'thales', 'thales', 'operator', '$2y$10$dGtIzyANyT6PuMWSOkuc9.7uBNENbKwXCXNq9V33BYuwwK/nQ78kG', 0),
('admin', 'thales', 'thales', 'admin', '$2y$10$dGtIzyANyT6PuMWSOkuc9.7uBNENbKwXCXNq9V33BYuwwK/nQ78kG', 0),
('superadmin', 'thales', 'thales', 'admin', '$2y$10$dGtIzyANyT6PuMWSOkuc9.7uBNENbKwXCXNq9V33BYuwwK/nQ78kG', 0);