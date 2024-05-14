--
-- Structure de la table PASSWORD
--

DROP TABLE IF EXISTS PASSWORD;

CREATE TABLE PASSWORD (
  password_id INT PRIMARY KEY NOT NULL,
  n INT NOT NULL,
  p INT NOT NULL,
  q INT NOT NULL,
  r INT NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Contenu de la table PASSWORD
--

INSERT INTO PASSWORD (password_id, n, p, q, r) VALUES
(1, 0, 8, 0, 0);