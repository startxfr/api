INSERT INTO  `ref_droit` (
`id_dt` ,
`nom_dt` ,
`desc_dt`
)
VALUES (
'17',  'Assistante commerciale',  'Assistance dans la gestion commerciale de l''entreprise'
);
UPDATE  `user` SET  `droit` =  '17' WHERE  `user`.`login` =  'jm';
UPDATE  `entreprise` SET  `tauxTVA_ent` =  '20' WHERE  `tauxTVA_ent` =  '19.6';


DELETE FROM `user_droits` WHERE `login` = 'nf' AND `droit` = 1410;
INSERT INTO  `user_droits` (`login` ,`droit`)
VALUES ('nf',  '1450'),
('nf',  '1452'),
('nf',  '1430'),
('nf',  '1435'),
('nf',  '1460'),
('nf',  '1462'),
('nf',  '1299'),
('nf',  '1599'),
('nf',  '1699');

DELETE FROM `user_droits` WHERE `login` = 'jm' AND `droit` = 1410;
INSERT INTO  `user_droits` (`login` ,`droit`)
VALUES ('jm',  '1450'),
('jm',  '1452'),
('jm',  '1430'),
('jm',  '1435'),
('jm',  '1460'),
('jm',  '1462'),
('jm',  '1299'),
('jm',  '1599'),
('jm',  '1699');
INSERT INTO  `user_droits` (`login` ,`droit`)
VALUES ('cl',  '1481'),
('cl',  '1482'),
('cl',  '1581'),
('cl',  '1582'),
('cl',  '1281'),
('cl',  '1282'),
('cl',  '1181'),
('cl',  '1182');
INSERT INTO  `user_droits` (`login` ,`droit`)
VALUES ('mg',  '1481'),
('mg',  '1482'),
('mg',  '1581'),
('mg',  '1582'),
('mg',  '1281'),
('mg',  '1282'),
('mg',  '1181'),
('mg',  '1182');
INSERT INTO  `user_droits` (`login` ,`droit`)
VALUES ('mg',  '1414'),
('mg',  '1514'),
('mg',  '1214'),
('mg',  '1114');
INSERT INTO  `user_droits` (`login` ,`droit`)
VALUES ('cl',  '1414'),
('cl',  '1514'),
('cl',  '1214'),
('cl',  '1114');