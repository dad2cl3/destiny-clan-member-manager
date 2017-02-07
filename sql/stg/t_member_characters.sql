/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : stg

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:52:07 PM
*/

-- ----------------------------
--  Table structure for t_member_characters
-- ----------------------------
DROP TABLE IF EXISTS "stg"."t_member_characters";
CREATE TABLE "stg"."t_member_characters" (
	"effective_date" date NOT NULL,
	"clan_id" int4 NOT NULL,
	"destiny_id" int8 NOT NULL,
	"character_id" int8 NOT NULL,
	"class_type" int4 NOT NULL,
	"last_played" date NOT NULL,
	"total_min_played" int4 NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "stg"."t_member_characters" OWNER TO "jachal";

