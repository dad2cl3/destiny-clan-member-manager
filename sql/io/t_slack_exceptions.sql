/*
 Navicat PostgreSQL Data Transfer

 Source Server         : io
 Source Server Version : 90502
 Source Host           : pgio.dad2cl3.com
 Source Database       : io
 Source Schema         : io

 Target Server Version : 90502
 File Encoding         : utf-8

 Date: 02/07/2017 12:57:14 PM
*/

-- ----------------------------
--  Table structure for t_slack_exceptions
-- ----------------------------
DROP TABLE IF EXISTS "io"."t_slack_exceptions";
CREATE TABLE "io"."t_slack_exceptions" (
	"exception_id" int4 NOT NULL DEFAULT nextval('t_slack_exceptions_exception_id_seq'::regclass),
	"slack_name" varchar(25) NOT NULL COLLATE "default",
	"destiny_name" varchar(25) NOT NULL COLLATE "default"
)
WITH (OIDS=FALSE);
ALTER TABLE "io"."t_slack_exceptions" OWNER TO "jachal";

COMMENT ON TABLE "io"."t_slack_exceptions" IS 'Table stores slack account names that do not match destiny account names.';

-- ----------------------------
--  Primary key structure for table t_slack_exceptions
-- ----------------------------
ALTER TABLE "io"."t_slack_exceptions" ADD PRIMARY KEY ("exception_id") NOT DEFERRABLE INITIALLY IMMEDIATE;

