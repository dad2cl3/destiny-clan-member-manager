/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:55:15 PM
*/

-- ----------------------------
--  Table structure for t_clans
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_clans";
CREATE TABLE "io"."t_clans" (
	"clan_id" int4 NOT NULL,
	"clan_name" varchar(100) NOT NULL COLLATE "default"
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_clans" OWNER TO "jachal";

-- ----------------------------
--  Primary key structure for table t_clans
-- ----------------------------
ALTER TABLE "io"."t_clans" ADD PRIMARY KEY ("clan_id") NOT DEFERRABLE INITIALLY IMMEDIATE;

