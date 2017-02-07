/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : archive

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:58:22 PM
*/

-- ----------------------------
--  Table structure for t_slack_accounts
-- ----------------------------
DROP TABLE IF EXISTS "archive"."t_slack_accounts";
CREATE TABLE "archive"."t_slack_accounts" (
	"effective_date" date,
	"slack_id" varchar(25) COLLATE "default",
	"slack_name" varchar(25) COLLATE "default",
	"destiny_name" varchar(25) COLLATE "default",
	"added" date,
	"disabled" date
)
WITH (OIDS=FALSE);
ALTER TABLE "archive"."t_slack_accounts" OWNER TO "jachal";

