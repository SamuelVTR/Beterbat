-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 25 Janvier 2019 à 23:38
-- Version du serveur :  5.6.20
-- Version de PHP :  5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `beterbat`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
`id` int(10) NOT NULL,
  `Nom` varchar(500) NOT NULL,
  `civilite` varchar(50) NOT NULL,
  `Adresse` varchar(100) NOT NULL,
  `codepostal` varchar(50) NOT NULL,
  `Ville` varchar(500) NOT NULL,
  `Pays` varchar(100) NOT NULL,
  `ref_cadastral` varchar(100) NOT NULL,
  `adresse_terrain` varchar(100) NOT NULL,
  `ville_terrain` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `client`
--

INSERT INTO `client` (`id`, `Nom`, `civilite`, `Adresse`, `codepostal`, `Ville`, `Pays`, `ref_cadastral`, `adresse_terrain`, `ville_terrain`) VALUES
(1, 'd', 'M. et Mme', 'd', 'd', 'd', 'd', '', '', ''),
(2, 'sdf', 'M. et Mme', 'sf', 'sdf', 'sdf', 'Ã´', '', '', ''),
(3, 'b', 'M. et Mme', 'b', 'b', 'b', 'b', '', '', ''),
(4, 'j', 'M. et Mme', 'j', 'j', 'j', 'j', '', '', ''),
(5, 's', 'M. et Mme', 's', 's', 's', 's', 's', '', ''),
(6, 'vautour samuel', 'M.', 'bois neuf', '97231', 'Le robert', 'Martinique', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `corps_de_metier`
--

CREATE TABLE IF NOT EXISTS `corps_de_metier` (
  `nom_corps_metier` varchar(100) NOT NULL,
  `charge_client` int(2) NOT NULL,
  `remarque` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `corps_de_metier`
--

INSERT INTO `corps_de_metier` (`nom_corps_metier`, `charge_client`, `remarque`) VALUES
('gros oeuvre', 1, ''),
('toiture', 0, ''),
('Electricite', 1, 'aucune'),
('peinture', 1, ''),
('Platerie', 1, 'placo');

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

CREATE TABLE IF NOT EXISTS `devis` (
`id` int(10) NOT NULL,
  `date` varchar(100) NOT NULL,
  `plan` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `commercial` varchar(100) NOT NULL,
  `adaptation_sol` varchar(5500) NOT NULL,
  `amenagement_acces` varchar(500) NOT NULL,
  `options` varchar(1000) NOT NULL,
  `metier_chargeclient` varchar(1000) NOT NULL,
  `assainissement` varchar(500) NOT NULL,
  `prices` varchar(500) NOT NULL,
  `pdf` varchar(1500) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `devis`
--

INSERT INTO `devis` (`id`, `date`, `plan`, `client`, `commercial`, `adaptation_sol`, `amenagement_acces`, `options`, `metier_chargeclient`, `assainissement`, `prices`, `pdf`) VALUES
(1, '2018-11-08', 'tibaume', 'd', 't', '[null,"04000","2000","2000","2000"]', '["aucun"]', '[["3",null,"1.07"],["5",null,"0.74"],["6",null,"4.09"]]', '[["gros oeuvre","8000 u20ac"]]', 'fosse-7373', '177266.11 â‚¬', '2018-11-08_tibaume_d_24354'),
(2, '2018-11-08', 'tibaume 2', 'd', 't', '[null,"7000","2500","3400","4120"]', '["encaillassement","7","4","100","2800"]', '[]', '[["gros oeuvre","9000 u20ac"]]', 'fosse-7373', '147475.92 â‚¬', '2018-11-08_tibaume 2_d_21548'),
(3, '2018-11-08', 'tibaume 3', 'vautour', 't', '[null,"4000.05","4000","5000","4000"]', '["encaillassement","1","11","100","1100"]', '[]', '[["gros oeuvre","8000 u20ac"]]', 'egout-250', '171229.32 â‚¬', '2018-11-08_tibaume 3_vautour_25321'),
(4, '2019-01-07', 'tibaume 3', 'j', 't', '["0.00","4000","4000","5000","4000"]', '["aucun"]', '[]', '[]', 'egout-250', '192316.25 â‚¬', '2019-01-07_tibaume 3_j_6779');

-- --------------------------------------------------------

--
-- Structure de la table `divers`
--

CREATE TABLE IF NOT EXISTS `divers` (
  `tva` varchar(100) NOT NULL,
  `encaillassement` varchar(100) NOT NULL,
  `betonnage` varchar(100) NOT NULL,
  `egout` varchar(100) NOT NULL,
  `fosse` varchar(100) NOT NULL,
  `tauxdommage` varchar(11) NOT NULL,
  `id` int(10) NOT NULL DEFAULT '1',
  `notice` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `divers`
--

INSERT INTO `divers` (`tva`, `encaillassement`, `betonnage`, `egout`, `fosse`, `tauxdommage`, `id`, `notice`) VALUES
('8.5', '100', '200', '250', '7373', '2.5', 1, '<h1>&nbsp; &nbsp;bvnbvn</h1><hr><hr><p><span style="font-size: 8px;">.................................................................................................................................................................................</span></p><p><span style="font-size: 8px;">.................................................................................................................................................................................</span></p><p><span style="font-size: 8px;">.................................................................................................................................................................................</span></p><p>.............................................................................................................................................................................</p><p>PREAMBULE Note explicative Toute maison individuelle doit &ecirc;tre construite dans le respect des articles R.111-1 et suivants (Livre Premier, &nbsp; Titre Premier,chapitre Premier, section II Dispositions g&eacute;n&eacute;rales applicables aux b&acirc;timents d&#39;habitation) du Code de la Construction et de l&#39;Habitation, relatifs notamment aux r&egrave;gles &eacute;l&eacute;mentaires de l&#39;hygi&egrave;ne et de la s&eacute;curit&eacute;. Ces articles imposent &eacute;galement des dispositions particuli&egrave;res concernant, par exemple, les caract&eacute;ristiques thermiques des logements. Les r&egrave;gles de construction relatives &agrave; la pr&eacute;vention du risque sismique pouvant varier selon la zone de sismicit&eacute; dans laquelle est &eacute;difi&eacute;e la construction, il convient de pr&eacute;ciser celles qui s&#39;appliquent D&#39;autre part, il doit &ecirc;tre pr&eacute;cis&eacute; si le projet se situe dans une zone soumise &agrave; une obligation d&#39;isolement acoustique vis-&agrave;-vis des bruits ext&eacute;rieurs. L&#39;attention des constructeurs est appel&eacute;e sur le probl&egrave;me du traitement des bois en particulier dans les zones affect&eacute;es par les termites ou autres insectes xylophages. Il leur appartient de se renseigner aupr&egrave;s de la mairie pour savoir si la zone de de construction est infest&eacute;e. Contrat avec fourniture de plan La pr&eacute;sente Notice Descriptive, vis&eacute;e &agrave; l&#39;Articles R.231-4 du Code de la Construction et de l&#39;Habitation, comporte la description et les caract&eacute;ristiques techniques de l&#39;immeuble conforme au plan propos&eacute;, et celles des travaux d&#39;adaptation au sol, des raccordements aux r&eacute;seaux divers, ainsi que les &eacute;quipements int&eacute;rieurs et ext&eacute;rieurs indispensables &agrave; l&#39;implantation et &agrave; l&#39;utilisation de cet immeuble. Elle mentionne le co&ucirc;t total du b&acirc;timent &agrave; construire qui est &eacute;gal &agrave; la somme du prix convenu au contrat et, s&#39;il y a lieu, du co&ucirc;t des travaux dont le Ma&icirc;tre d&#39;ouvrage se r&eacute;serve l&#39;ex&eacute;cution. La distinction est donc faite entre les &eacute;l&eacute;ments compris dans le prix convenu et les &eacute;l&eacute;ments non compris dans le prix convenu : pour ces derniers le co&ucirc;t est pr&eacute;cis&eacute; dans la colonne correspondante. Aucun des ouvrages ou fournitures mentionn&eacute;s dans la Notice Descriptive ne peut &ecirc;tre omis ; si ils ne sont pas compris dans le prix convenu, ils doivent faire l&#39;objet d&#39;une pr&eacute;cision de leur co&ucirc;t dans la colonne correspondante. Aucun des ouvrages ou fournitures mentionn&eacute;s dans la Notice Descriptive ne peut &ecirc;tre omis ; s&#39;ils ne sont pas compris dans le prix convenu, ils doivent faire l&#39;objet d&#39;une pr&eacute;cision de leur co&ucirc;t dans la correspondante. Aucun des ouvrages ou fournitures mentionn&eacute;s dans la Notice descriptive ne peut &ecirc;tre omis ; s&#39;ils ne sont pas compris dans le prix convenu ils doivent faire l&#39;objet d&#39;une pr&eacute;cision d leur co&ucirc;t dans la colonne correspondante. Si le contrat pr&eacute;voit des ouvrages ou fournitures qui ne figurent pas dans la notice, ils doivent faire l&#39;objet, d&#39;une annexe &agrave; la Notice Descriptive et leur co&ucirc;t doit y figurer. L&#39;annexe paraph&eacute;e par les deux co-contractants doit comporter, avec la m&ecirc;me pr&eacute;cision que celle de la notice, la description de ces ouvrages ou fournitures ainsi que leur co&ucirc;t (par exemple : isolation acoustique sup&eacute;rieure aux valeurs r&eacute;glementaires, &eacute;l&eacute;ments d&#39;&eacute;quipement de dalle de bains et de salle d&#39;eau, balcon, cl&ocirc;ture, am&eacute;nagement d&#39;espaces verts, etc.&hellip;)</p><p>&nbsp;</p><hr><p>&nbsp;D&eacute;finition de la zone de construction</p><p>Zone sismique = V</p><p>Isolement de fa&ccedil;ade vis-&agrave;-vis des bruits ext&eacute;rieurs (arr&ecirc;t&eacute; du 06/10/1978) : isolement = Sans Objet</p><p>Zone infest&eacute;e par les termites = pas de reconnaissance mais traitement pr&eacute;ventive garantie 5 ans</p>');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
`id` int(10) NOT NULL,
  `nom` varchar(200) CHARACTER SET latin1 NOT NULL,
  `sous_groupe` varchar(200) CHARACTER SET latin1 NOT NULL,
  `cout` decimal(10,2) NOT NULL,
  `sans_quantite` int(11) NOT NULL,
  `tariflieplan` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `options`
--

INSERT INTO `options` (`id`, `nom`, `sous_groupe`, `cout`, `sans_quantite`, `tariflieplan`) VALUES
(8, 'volet r', 'Volet bois par modÃ¨le', '4000.00', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `pdf`
--

CREATE TABLE IF NOT EXISTS `pdf` (
`id` int(10) NOT NULL,
  `Nom` varchar(500) NOT NULL,
  `mime` varchar(500) NOT NULL,
  `data` blob NOT NULL,
  `nom_plan` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `plan`
--

CREATE TABLE IF NOT EXISTS `plan` (
`Id` int(10) NOT NULL,
  `Nom_plan` varchar(500) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Branchement` varchar(500) NOT NULL,
  `prix_HT` varchar(500) NOT NULL,
  `Sous_groupe` varchar(500) NOT NULL,
  `Travaux` varchar(8000) NOT NULL,
  `cout_par_corps_metier` varchar(8000) NOT NULL,
  `pdf` varchar(500) NOT NULL,
  `cout_option_specifique` varchar(8000) NOT NULL,
  `options` mediumtext NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `plan`
--

INSERT INTO `plan` (`Id`, `Nom_plan`, `Description`, `Branchement`, `prix_HT`, `Sous_groupe`, `Travaux`, `cout_par_corps_metier`, `pdf`, `cout_option_specifique`, `options`) VALUES
(22, 'rtygggd x', 'rty', 'r', '0.01', 'DIFFUS', '["travailu00e9u00f9u00e7","travail 1"]', '[["thze metier","0.06"],["gros oeuvre","0.03"]]', '../uploads/5b9fb0b3e5b036.76930469.pdf', '', '[["3","1.00"],["5","0.75"]]'),
(23, 'plan', 'azeazeaz', '', '150000', 'DIFFUS', '["travail2","trav11","travailu00e9u00f9u00e7","travail 1"]', '[["thzemetier","8000"],["gros oeuvre","414"],["metier 2","4141"]]', '../uploads/5baa57d8434496.04612683.pdf', '', '[["3","1000"],["5","1700"],["6","0111"],["7","400"]]'),
(24, 'tibaume', 'f5', '', '150000', 'DIFFUS', '["travail2","trav11","travailu00e9u00f9u00e7","travail 1"]', '[["gros oeuvre","8000"]]', '../uploads/5be45300b6b750.36665012.pdf', '["04000","2000","2000","2000"]', '[["3","1.07"],["5","0.74"],["6","4.09"]]'),
(25, 'tibaume 2', 'f4', '', '130000', 'DIFFUS', '["tolle","tuile","carreaux","dalle","borne edf","peinture bleu","protection sol"]', '[["gros oeuvre","9000"],["Electricite","4000"],["peinture","4000"]]', '../uploads/plan1.pdf', '["7000","2500","3400","4120"]', '[]'),
(26, 'tibaume 3', 'f5', '', '160000', 'DIFFUS', '["carreaux","dalle","peinture bleu","protection sol"]', '[["gros oeuvre","8000"],["Electricite",""]]', '../uploads/plan1.pdf', '["4000","4000","5000","4000"]', '[]');

-- --------------------------------------------------------

--
-- Structure de la table `referentiel_travaux`
--

CREATE TABLE IF NOT EXISTS `referentiel_travaux` (
  `nom_corps_metier` varchar(50) NOT NULL,
  `nom_travail` varchar(50) NOT NULL,
  `caracteristique` varchar(100) NOT NULL,
  `chargeclient` int(2) NOT NULL,
  `nom_regroupement` varchar(50) NOT NULL,
  `cout` float(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `referentiel_travaux`
--

INSERT INTO `referentiel_travaux` (`nom_corps_metier`, `nom_travail`, `caracteristique`, `chargeclient`, `nom_regroupement`, `cout`) VALUES
('toiture', 'tolle', '', 0, 'toiture', 0.00),
('toiture', 'tuile', '', 0, 'toiture', 0.00),
('gros oeuvre', 'carreaux', '', 0, 'res de chaussez', 0.00),
('gros oeuvre', 'dalle', '', 0, 'sous sol', 0.00),
('Electricite', 'borne edf', '', 1, 'electricite', 500.00),
('peinture', 'peinture bleu', '', 0, 'interieur', 0.00),
('peinture', 'protection sol', '', 0, 'exterieur', 0.00),
('Platerie', 'mortier', '', 0, 'platerie', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `regroupement`
--

CREATE TABLE IF NOT EXISTS `regroupement` (
  `nom_corps_metier` varchar(50) NOT NULL,
  `nom_regroupement` varchar(50) NOT NULL,
  `remarque` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `regroupement`
--

INSERT INTO `regroupement` (`nom_corps_metier`, `nom_regroupement`, `remarque`) VALUES
('Electricite', 'electricite', ''),
('toiture', 'toiture', ''),
('gros oeuvre', 'res de chaussez', ''),
('gros oeuvre', 'sous sol', ''),
('peinture', 'interieur', ''),
('peinture', 'exterieur', ''),
('Platerie', 'platerie', '');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
`id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `statut` varchar(15) NOT NULL,
  `password` varchar(50) NOT NULL,
  `initial` varchar(10) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `login`, `statut`, `password`, `initial`, `nom`) VALUES
(6, 't', 'Administrateur', 'VnNHYTlvY2FDZU9iRFg3eVlsTUhGUT09', 't', 't'),
(9, 'a', 'Commercial', 'ODNTSVNvcGdvaWppWU5oWHAwZ01SZz09', 'a', 'a');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `client`
--
ALTER TABLE `client`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `corps_de_metier`
--
ALTER TABLE `corps_de_metier`
 ADD PRIMARY KEY (`nom_corps_metier`);

--
-- Index pour la table `devis`
--
ALTER TABLE `devis`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `divers`
--
ALTER TABLE `divers`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `tva` (`tva`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `pdf`
--
ALTER TABLE `pdf`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `plan`
--
ALTER TABLE `plan`
 ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `Nom_plan` (`Nom_plan`);

--
-- Index pour la table `referentiel_travaux`
--
ALTER TABLE `referentiel_travaux`
 ADD PRIMARY KEY (`nom_travail`);

--
-- Index pour la table `regroupement`
--
ALTER TABLE `regroupement`
 ADD PRIMARY KEY (`nom_regroupement`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `devis`
--
ALTER TABLE `devis`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `pdf`
--
ALTER TABLE `pdf`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `plan`
--
ALTER TABLE `plan`
MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
