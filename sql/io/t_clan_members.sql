/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:54:59 PM
*/

-- ----------------------------
--  Table structure for t_clan_members
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_clan_members";
CREATE TABLE "io"."t_clan_members" (
	"clan_id" int4 NOT NULL,
	"destiny_id" int8 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_clan_members" OWNER TO "jachal";

