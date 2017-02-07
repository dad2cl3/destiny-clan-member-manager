/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:57:01 PM
*/

-- ----------------------------
--  Table structure for t_slack_accounts
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_slack_accounts";
CREATE TABLE "io"."t_slack_accounts" (
	"slack_id" varchar(25) NOT NULL COLLATE "default",
	"slack_name" varchar(25) NOT NULL COLLATE "default",
	"added" date NOT NULL DEFAULT now(),
	"disabled" date
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_slack_accounts" OWNER TO "jachal";

-- ----------------------------
--  Indexes structure for table t_slack_accounts
-- ----------------------------
CREATE INDEX  "idx_upper_slack_name" ON "io"."t_slack_accounts" USING btree(upper(slack_name::text) COLLATE "default" "pg_catalog"."text_ops" ASC NULLS LAST);

