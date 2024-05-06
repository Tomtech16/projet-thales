--
-- Structure de la table PASSWORD
--

CREATE TABLE checklist.PASSWORD (
  passwordkey INT PRIMARY KEY NOT NULL,
  n INT NOT NULL,
  p INT NOT NULL,
  q INT NOT NULL,
  r INT NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Contenu de la table PASSWORD
--

INSERT INTO PASSWORD (passwordkey, n, p, q, r) VALUES
(1, 0, 8, 0, 0);