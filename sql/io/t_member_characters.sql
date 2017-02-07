/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:56:27 PM
*/

-- ----------------------------
--  Table structure for t_member_characters
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_member_characters";
CREATE TABLE "io"."t_member_characters" (
	"destiny_id" int8 NOT NULL,
	"character_id" int8 NOT NULL,
	"deleted" date
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_member_characters" OWNER TO "jachal";

