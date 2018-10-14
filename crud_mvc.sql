SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(130) NOT NULL,
  `cep` varchar(8) DEFAULT NULL,
  `number` varchar(8) DEFAULT NULL,
  `road` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(25) DEFAULT NULL,
  `neigh` varchar(100) DEFAULT NULL,
  `complement` varchar(150) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `phones`
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `phones`
  ADD CONSTRAINT `phones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

