/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50721
 Source Host           : localhost:3306
 Source Schema         : ugmpress

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 02/02/2022 00:38:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for notifikasi
-- ----------------------------
DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_pembuat` int(11) NULL DEFAULT NULL,
  `id_user_kepada` int(11) NULL DEFAULT NULL,
  `id_draft` int(11) NULL DEFAULT NULL,
  `ket` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `is_read` binary(1) NULL DEFAULT 0,
  `read_at` timestamp(0) NULL DEFAULT NULL,
  `is_starred` binary(1) NULL DEFAULT 0,
  `starred_at` timestamp(0) NULL DEFAULT NULL,
  `creation_date` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 87 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
