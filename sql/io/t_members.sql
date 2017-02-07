/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:56:44 PM
*/

-- ----------------------------
--  Table structure for t_members
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_members";
CREATE TABLE "io"."t_members" (
	"destiny_id" int8 NOT NULL,
	"destiny_name" varchar(25) NOT NULL COLLATE "default",
	"bungie_id" int4,
	"bungie_name" varchar(25) COLLATE "default",
	"added" date NOT NULL DEFAULT now(),
	"deleted" date,
	"approval_date" date
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_members" OWNER TO "jachal";

-- ----------------------------
--  Indexes structure for table t_members
-- ----------------------------
CREATE INDEX  "idx_upper_destiny_name" ON "io"."t_members" USING btree(upper(destiny_name::text) COLLATE "default" "pg_catalog"."text_ops" ASC NULLS LAST);

