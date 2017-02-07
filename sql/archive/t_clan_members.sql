/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : archive

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:58:14 PM
*/

-- ----------------------------
--  Table structure for t_clan_members
-- ----------------------------
DROP TABLE IF EXISTS "archive"."t_clan_members";
CREATE TABLE "archive"."t_clan_members" (
	"effective_date" date,
	"clan_id" int4,
	"clan_name" varchar(100) COLLATE "default",
	"bungie_id" int4,
	"bungie_name" varchar(25) COLLATE "default",
	"destiny_id" int8,
	"destiny_name" varchar(25) COLLATE "default",
	"member_added" date,
	"member_deleted" date,
	"approval_date" date,
	"character_id" int8,
	"class_type" int4,
	"class_name" varchar(20) COLLATE "default",
	"class_hash" varchar(20) COLLATE "default",
	"char_added" date,
	"char_deleted" date,
	"last_played" timestamp(6) WITH TIME ZONE,
	"total_min_played" int4
)
WITH (OIDS=FALSE);
ALTER TABLE "archive"."t_clan_members" OWNER TO "jachal";

