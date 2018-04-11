SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `d_ah_configuration`
-- ----------------------------
DROP TABLE IF EXISTS d_ah_configuration;

CREATE TABLE `d_ah_configuration` (
  ID          INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  PARENT_ID   INT,
  TITLE       VARCHAR(255),
  CODE       VARCHAR(255)
);

DROP TABLE IF EXISTS d_ah_option;

CREATE TABLE `d_ah_option` (
  ID             INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  CONFIGURATION_ID    INT,
  TITLE          VARCHAR(255),
  CODE       VARCHAR(255),
  TYPE       VARCHAR(255),
  MULTIPLE char(1) not null DEFAULT 'N'
);

DROP TABLE IF EXISTS d_ah_option_value;

CREATE TABLE `d_ah_option_value` (
  ID           INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  OPTION_ID    INT,
  VALUE        LONGTEXT
);