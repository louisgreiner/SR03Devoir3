-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:12889
-- Généré le : Dim 30 mai 2021 à 10:23
-- Version du serveur :  5.7.32
-- Version de PHP : 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données : `sr03`
--

-- --------------------------------------------------------

--
-- Structure de la table `connection_errors`
--

DROP TABLE IF EXISTS `connection_errors`;
CREATE TABLE `connection_errors` (
  `ip` varchar(20) NOT NULL,
  `error_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `connection_errors`
--

INSERT INTO `connection_errors` (`ip`, `error_date`) VALUES
('127.0.0.1', '2021-05-30 09:46:56'),
('127.0.0.1', '2021-05-30 10:09:36'),
('127.0.0.1', '2021-05-30 10:21:17');

-- --------------------------------------------------------

--
-- Structure de la table `MESSAGES`
--

DROP TABLE IF EXISTS `MESSAGES`;
CREATE TABLE `MESSAGES` (
  `id_msg` int(11) NOT NULL,
  `id_user_to` int(11) NOT NULL,
  `id_user_from` int(11) NOT NULL,
  `sujet_msg` varchar(100) NOT NULL,
  `corps_msg` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `MESSAGES`
--

INSERT INTO `MESSAGES` (`id_msg`, `id_user_to`, `id_user_from`, `sujet_msg`, `corps_msg`) VALUES
(1, 4, 1, 'Bienvenue chez BankMate !', 'Bienvenue chez BankMate Angela ! Nous espérons que votre expérience sera la plus agréable possible !'),
(2, 5, 1, 'Bienvenue chez BankMate !', 'Bienvenue chez BankMate Donald ! Nous espérons que votre expérience sera la plus agréable possible !'),
(3, 1, 2, 'Test XSS', 'Bonjour Benjamin, voici le message de test dont nous avions parlé. <script>alert(\'hello\')</script>.'),
(4, 3, 2, 'Test XSS', 'Bonjour Louis, voici le message de test dont nous avions parlé. <script>alert(\'hello\')</script>.'),
(5, 2, 1, 'Test XSS', 'Bonjour Yuheng, voici le message de test dont nous avions parlé. <script>alert(\'hello\')</script>.'),
(6, 1, 3, 'Update : mot de passe chiffré', 'Bonjour Benjamin, les mots de passe ont bien été chiffrés dans la BDD.'),
(7, 3, 2, 'Update : mot de passe chiffré', 'Bonjour Louis, les mots de passe ont bien été chiffrés dans la BDD.'),
(8, 1, 3, 'Nouveaux clients', 'Bonjour Benjamin, nous avons deux nouveaux clients, M. Trump et Mme Merkel, auxquels il faudrait que tu souhaites la bienvenue.');

-- --------------------------------------------------------

--
-- Structure de la table `transfer_history`
--

DROP TABLE IF EXISTS `transfer_history`;
CREATE TABLE `transfer_history` (
  `id_transaction` int(11) NOT NULL,
  `compte_emetteur` varchar(20) NOT NULL,
  `compte_destinataire` varchar(20) NOT NULL,
  `montant` decimal(11,2) NOT NULL,
  `date_transaction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `transfer_history`
--

INSERT INTO `transfer_history` (`id_transaction`, `compte_emetteur`, `compte_destinataire`, `montant`, `date_transaction`) VALUES
(30, '8247285830', '4964011994', '12.00', '2021-05-30 10:22:20');

-- --------------------------------------------------------

--
-- Structure de la table `USERS`
--

DROP TABLE IF EXISTS `USERS`;
CREATE TABLE `USERS` (
  `id_user` int(11) NOT NULL,
  `login` varchar(10) NOT NULL,
  `mot_de_passe` varchar(512) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `numero_compte` varchar(20) NOT NULL,
  `profil_user` varchar(7) NOT NULL,
  `solde_compte` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `USERS`
--

INSERT INTO `USERS` (`id_user`, `login`, `mot_de_passe`, `nom`, `prenom`, `numero_compte`, `profil_user`, `solde_compte`) VALUES
(1, 'bmissaou', '$2y$10$YeEVy2kzzLgLlnUtk5D3.uzZMVrpleFgpPk5qVPwgW3wJe2IdmVli', 'Missaoui', 'Benjamin', '8247285830', 'EMPLOYE', '127001.37'),
(2, 'yuchen', '$2y$10$0CswdNYygB7rTGL4OU3gC.CaXIgimAO8cLFqyst4siMkeJBG7AFDa', 'Chen', 'Yuheng', '4964011994', 'EMPLOYE', '144006.11'),
(3, 'lgreiner', '$2y$10$Pn1JkIwrNvHpOThuNPg./.GJlKxiKzz57nop3iiWGeG01Wi6wIKu.', 'Greiner', 'Louis', '7873234879', 'EMPLOYE', '141482.29'),
(4, 'amerkel', '$2y$10$mEcCDnboJx/gFN0uSOCDOOgxoAWwHx.mNOShFfdB1vfNQnPwwWEee', 'Merkel', 'Angela', '5789446841', 'CLIENT', '341.56'),
(5, 'dtrump', '$2y$10$VSEQrPpe7ch69ZPT1hPwV.Ee0pBtnjqA9A2L8n74V2LSwPlIDbHbm', 'Trump', 'Donald', '6201640760', 'CLIENT', '556.68');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `connection_errors`
--
ALTER TABLE `connection_errors`
  ADD PRIMARY KEY (`ip`,`error_date`);

--
-- Index pour la table `MESSAGES`
--
ALTER TABLE `MESSAGES`
  ADD PRIMARY KEY (`id_msg`),
  ADD KEY `id_user_from` (`id_user_from`),
  ADD KEY `id_user_to` (`id_user_to`);

--
-- Index pour la table `transfer_history`
--
ALTER TABLE `transfer_history`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `compte_emetteur` (`compte_emetteur`),
  ADD KEY `compte_destinataire` (`compte_destinataire`);

--
-- Index pour la table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `numero_compte` (`numero_compte`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `MESSAGES`
--
ALTER TABLE `MESSAGES`
  MODIFY `id_msg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `transfer_history`
--
ALTER TABLE `transfer_history`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `MESSAGES`
--
ALTER TABLE `MESSAGES`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_user_from`) REFERENCES `USERS` (`id_user`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_user_to`) REFERENCES `USERS` (`id_user`);

--
-- Contraintes pour la table `transfer_history`
--
ALTER TABLE `transfer_history`
  ADD CONSTRAINT `transfer_history_ibfk_1` FOREIGN KEY (`compte_emetteur`) REFERENCES `USERS` (`numero_compte`),
  ADD CONSTRAINT `transfer_history_ibfk_2` FOREIGN KEY (`compte_destinataire`) REFERENCES `USERS` (`numero_compte`);
