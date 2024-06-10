---
--- Erase USERS table if exists
---

DROP TABLE IF EXISTS USERS;

--
-- Create USERS table
--

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
-- Insert data into USERS table
--

INSERT INTO USERS (user_id, username, firstname, lastname, profile, password, attempts) VALUES
(1, 'thales06', 'thales06', 'thales06', 'superadmin', '$2y$10$uM4Dr0WDEGCA/oaQsatk.eAaNC1iQRlpzXHAj8QMVgl9Mig3arEfS', 0);