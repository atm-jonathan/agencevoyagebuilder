ALTER TABLE `llx_formulevoyage_formule` ADD `import_key`  varchar(14) NULL AFTER `fk_country`;
ALTER TABLE `llx_formulevoyage_formule` ADD COLUMN `fk_soc` INT(11) DEFAULT NULL AFTER `import_key`;
INSERT INTO `llx_c_type_contact` (`element`, `source`, `code`, `libelle`, `active`, `module`, `position`) VALUES ('formule', 'external', 'CUSTOMER', 'Voyageur', '1', NULL, '0')