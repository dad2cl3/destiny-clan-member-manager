/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : stg

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:52:15 PM
*/

-- ----------------------------
--  Table structure for t_slack_accounts
-- ----------------------------
DROP TABLE IF EXISTS "stg"."t_slack_accounts";
CREATE TABLE "stg"."t_slack_accounts" (
	"effective_date" date NOT NULL,
	"slack_id" varchar(25) NOT NULL COLLATE "default",
	"user_name" varchar(50) NOT NULL COLLATE "default"
)
WITH (OIDS=FALSE);
ALTER TABLE "stg"."t_slack_accounts" OWNER TO "jachal";

COMMENT ON TABLE "stg"."t_slack_accounts" IS 'Raw staging of Slack accounts';

