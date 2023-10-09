CREATE TABLE llx_c_mode_transport(
    -- BEGIN MODULEBUILDER FIELDS
     rowid     integer     PRIMARY KEY,
     pos   	tinyint DEFAULT 0 NOT NULL,
     code    	varchar(16) NOT NULL,
     label 	varchar(128),
     c_level   tinyint DEFAULT 0 NOT NULL,
     active  	tinyint DEFAULT 1  NOT NULL
    -- END MODULEBUILDER FIELDS
) ENGINE=innodb;