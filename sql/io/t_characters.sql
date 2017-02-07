/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:54:22 PM
*/

-- ----------------------------
--  Table structure for t_characters
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_characters";
CREATE TABLE "io"."t_characters" (
	"character_id" int8 NOT NULL,
	"class_type" int4 NOT NULL,
	"last_played" date,
	"total_min_played" int4,
	"added" date DEFAULT ('now'::text)::date,
	"deleted" date
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_characters" OWNER TO "jachal";

