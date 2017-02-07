/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : stg

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:59:01 PM
*/

-- ----------------------------
--  Table structure for t_clan_members
-- ----------------------------
DROP TABLE IF EXISTS "stg"."t_clan_members";
CREATE TABLE "stg"."t_clan_members" (
	"effective_date" date NOT NULL,
	"clan_id" int4 NOT NULL,
	"bungie_id" int4,
	"bungie_name" varchar(100) DEFAULT NULL::character varying COLLATE "default",
	"destiny_id" int8 NOT NULL,
	"destiny_name" varchar(100) NOT NULL COLLATE "default",
	"approval_date" date NOT NULL
)
WITH (OIDS=FALSE);
ALTER TABLE "stg"."t_clan_members" OWNER TO "jachal";

