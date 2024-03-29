CREATE DATABASE VOTE;
USE VOTE;


CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(255) NOT NULL UNIQUE,
    country VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    zipcode varchar(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    token_accepted BOOLEAN NOT NULL,
    conditions_accepted BOOLEAN NOT NULL
);


CREATE TABLE poll (
    poll_id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    user_id INT,
    start_date DATETIME,
    end_date DATETIME,
    poll_state ENUM('active','blocked','not_started','finished') ,
    question_visibility ENUM('public','private','hidden') ,
    results_visibility ENUM('public','private','hidden') ,
    path_image varchar(255) DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);


CREATE TABLE poll_options (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    option_text TEXT NOT NULL,
    poll_id INT NOT NULL,
    path_image varchar(255),
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
);

CREATE TABLE user_vote (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    poll_id INT NOT null,
    user_type ENUM('registered', 'guest') NOT NULL,
    guest_email VARCHAR(255),
    hash_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
   
);



CREATE TABLE voted_option (
    option_id INT,
    hash VARCHAR(255) NOT NULL,
    FOREIGN KEY (option_id) REFERENCES poll_options(option_id)
);

CREATE TABLE invitation (
    invitation_id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    guest_email VARCHAR(255),
    sent_date DATETIME,
    token varchar(255),
    token_accepted BOOLEAN NOT NULL,
    blocked TINYINT(1) NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
    
);


CREATE TABLE SEND_EMAIL (
    id INT AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
);


CREATE TABLE IF NOT EXISTS pais (
  id int(11) NOT NULL AUTO_INCREMENT,
  paisnombre varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  paisprefijo varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=247 ;


INSERT INTO pais (id, paisnombre, paisprefijo) VALUES
(1, 'Australia', '+61'),
(2, 'Austria', '+43'),
(3, 'Azerbaiyán', '+994'),
(4, 'Anguilla', '+1-264'),
(5, 'Argentina', '+54'),
(6, 'Armenia', '+374'),
(7, 'Bielorrusia', '+375'),
(8, 'Belice', '+501'),
(9, 'Bélgica', '+32'),
(10, 'Bermudas', '+1-441'),
(11, 'Bulgaria', '+359'),
(12, 'Brasil', '+55'),
(13, 'Reino Unido', '+44'),
(14, 'Hungría', '+36'),
(15, 'Vietnam', '+84'),
(16, 'Haiti', '+509'),
(17, 'Guadalupe', '+590'),
(18, 'Alemania', '+49'),
(19, 'Países Bajos, Holanda', '+31'),
(20, 'Grecia', '+30'),
(21, 'Georgia', '+995'),
(22, 'Dinamarca', '+45'),
(23, 'Egipto', '+20'),
(24, 'Israel', '+972'),
(25, 'India', '+91'),
(26, 'Irán', '+98'),
(27, 'Irlanda', '+353'),
(28, 'España', '+34'),
(29, 'Italia', '+39'),
(30, 'Kazajstán', '+7'),
(31, 'Camerún', '+237'),
(32, 'Canadá', '+1'),
(33, 'Chipre', '+357'),
(34, 'Kirguistán', '+996'),
(35, 'China', '+86'),
(36, 'Costa Rica', '+506'),
(37, 'Kuwait', '+965'),
(38, 'Letonia', '+371'),
(39, 'Libia', '+218'),
(40, 'Lituania', '+370'),
(41, 'Luxemburgo', '+352'),
(42, 'México', '+52'),
(43, 'Moldavia', '+373'),
(44, 'Mónaco', '+377'),
(45, 'Nueva Zelanda', '+64'),
(46, 'Noruega', '+47'),
(47, 'Polonia', '+48'),
(48, 'Portugal', '+351'),
(49, 'Reunión', '+262'),
(50, 'Rusia', '+7'),
(51, 'El Salvador', '+503'),
(52, 'Eslovaquia', '+421'),
(53, 'Eslovenia', '+386'),
(54, 'Surinam', '+597'),
(55, 'Estados Unidos', '+1'),
(56, 'Tadjikistan', '+992'),
(57, 'Turkmenistan', '+993'),
(58, 'Islas Turcas y Caicos', '+1-649'),
(59, 'Turquía', '+90'),
(60, 'Uganda', '+256'),
(61, 'Uzbekistán', '+998'),
(62, 'Ucrania', '+380'),
(63, 'Finlandia', '+358'),
(64, 'Francia', '+33'),
(65, 'República Checa', '+420'),
(66, 'Suiza', '+41'),
(67, 'Suecia', '+46'),
(68, 'Estonia', '+372'),
(69, 'Corea del Sur', '+82'),
(70, 'Japón', '+81'),
(71, 'Croacia', '+385'),
(72, 'Rumanía', '+40'),
(73, 'Hong Kong', '+852'),
(74, 'Indonesia', '+62'),
(75, 'Jordania', '+962'),
(76, 'Malasia', '+60'),
(77, 'Singapur', '+65'),
(78, 'Taiwan', '+886'),
(79, 'Bosnia y Herzegovina', '+387'),
(80, 'Bahamas', '+1'),
(81, 'Chile', '+56'),
(82, 'Colombia', '+57'),
(83, 'Islandia', '+354'),
(84, 'Corea del Norte', '+850'),
(85, 'Macedonia', '+389'),
(86, 'Malta', '+356'),
(87, 'Pakistán', '+92'),
(88, 'Papúa-Nueva Guinea', '+675'),
(89, 'Perú', '+51'),
(90, 'Filipinas', '+63'),
(91, 'Arabia Saudita', '+966'),
(92, 'Tailandia', '+66'),
(93, 'Emiratos árabes Unidos', '+971'),
(94, 'Groenlandia', '+299'),
(95, 'Venezuela', '+58'),
(96, 'Zimbabwe', '+263'),
(97, 'Kenia', '+254'),
(98, 'Algeria', '+213'),
(99, 'Líbano', '+961'),
(100, 'Botsuana', '+267'),
(101, 'Tanzania', '+255'),
(102, 'Namibia', '+264'),
(103, 'Ecuador', '+593'),
(104, 'Marruecos', '+212'),
(105, 'Ghana', '+233'),
(106, 'Siria', '+963'),
(107, 'Nepal', '+977'),
(108, 'Mauritania', '+222'),
(109, 'Seychelles', '+248'),
(110, 'Paraguay', '+595'),
(111, 'Uruguay', '+598'),
(112, 'Congo (Brazzaville)', '+242'),
(113, 'Cuba', '+53'),
(114, 'Albania', '+355'),
(115, 'Nigeria', '+234'),
(116, 'Zambia', '+260'),
(117, 'Mozambique', '+258'),
(119, 'Angola', '+244'),
(120, 'Sri Lanka', '+94'),
(121, 'Etiopía', '+251'),
(122, 'Túnez', '+216'),
(123, 'Bolivia', '+591'),
(124, 'Panamá', '+507'),
(125, 'Malawi', '+265'),
(126, 'Liechtenstein', '+423'),
(127, 'Bahrein', '+973'),
(128, 'Barbados', '+1246'),
(130, 'Chad', '+235'),
(131, 'Man, Isla de', '+44'),
(132, 'Jamaica', '+1876'),
(133, 'Malí', '+223'),
(134, 'Madagascar', '+261'),
(135, 'Senegal', '+221'),
(136, 'Togo', '+228'),
(137, 'Honduras', '+504'),
(138, 'República Dominicana', '+1809'),
(139, 'Mongolia', '+976'),
(140, 'Irak', '+964'),
(141, 'Sudáfrica', '+27'),
(142, 'Aruba', '+297'),
(143, 'Gibraltar', '+350'),
(144, 'Afganistán', '+93'),
(145, 'Andorra', '+376'),
(147, 'Antigua y Barbuda', '+1268'),
(149, 'Bangladesh', '+880'),
(151, 'Benín', '+229'),
(152, 'Bután', '+975'),
(154, 'Islas Virgenes Británicas', '+1284'),
(155, 'Brunéi', '+673'),
(156, 'Burkina Faso', '+226'),
(157, 'Burundi', '+257'),
(158, 'Camboya', '+855'),
(159, 'Cabo Verde', '+238'),
(164, 'Comores', '+269'),
(165, 'Congo (Kinshasa)', '+243'),
(166, 'Cook, Islas', '+682'),
(168, 'Costa de Marfil', '+225'),
(169, 'Yibuti', '+253'),
(171, 'Timor Oriental', '+670'),
(172, 'Guinea Ecuatorial', '+240'),
(173, 'Eritrea', '+291'),
(175, 'Feroe, Islas', '+298'),
(176, 'Fiyi', '+679'),
(178, 'Polinesia Francesa', '+689'),
(180, 'Gabón', '+241'),
(181, 'Gambia', '+220'),
(184, 'Granada', '+1473'),
(185, 'Guatemala', '+502'),
(186, 'Guernsey', '+44'),
(187, 'Guinea', '+224'),
(188, 'Guinea-Bissau', '+245'),
(189, 'Guyana', '+592'),
(193, 'Jersey', '+44'),
(195, 'Kiribati', '+686'),
(196, 'Laos', '+856'),
(197, 'Lesotho', '+266'),
(198, 'Liberia', '+231'),
(200, 'Maldivas', '+960'),
(201, 'Martinica', '+596'),
(202, 'Mauricio', '+230'),
(205, 'Myanmar', '+95'),
(206, 'Nauru', '+674'),
(207, 'Antillas Holandesas', '+599'),
(208, 'Nueva Caledonia', '+687'),
(209, 'Nicaragua', '+505'),
(210, 'Níger', '+227'),
(212, 'Norfolk Island', '+672'),
(213, 'Omán', '+968'),
(215, 'Isla Pitcairn', '+64'),
(216, 'Qatar', '+974'),
(217, 'Ruanda', '+250'),
(218, 'Santa Elena', '+290'),
(219, 'San Cristobal y Nevis', '+1869'),
(220, 'Santa Lucía', '+1758'),
(221, 'San Pedro y Miquelón', '+508'),
(222, 'San Vincente y Granadinas', '+1784'),
(223, 'Samoa', '+685'),
(224, 'San Marino', '+378'),
(225, 'San Tomé y Príncipe', '+239'),
(226, 'Serbia y Montenegro', '+381'),
(227, 'Sierra Leona', '+232'),
(228, 'Islas Salomón', '+677'),
(229, 'Somalia', '+252'),
(232, 'Sudán', '+249'),
(234, 'Swazilandia', '+268'),
(235, 'Tokelau', '+690'),
(236, 'Tonga', '+676'),
(237, 'Trinidad y Tobago', '+1868'),
(239, 'Tuvalu', '+688'),
(240, 'Vanuatu', '+678'),
(241, 'Wallis y Futuna', '+681'),
(242, 'Sáhara Occidental', '+212'),
(243, 'Yemen', '+967'),
(246, 'Puerto Rico', '+1787');