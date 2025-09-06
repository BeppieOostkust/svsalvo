-- Run this SQL script manually in your MySQL database to safely drop all tables

SET FOREIGN_KEY_CHECKS = 0;

-- Drop all tables (adjust table names as needed)
DROP TABLE IF EXISTS `feedback_votes`;
DROP TABLE IF EXISTS `feedback_comments`;
DROP TABLE IF EXISTS `feedback`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `user_legal_acceptances`;
DROP TABLE IF EXISTS `legal_documents`;
DROP TABLE IF EXISTS `match_registrations`;
DROP TABLE IF EXISTS `wedstrijd_gebruikers_scores`;
DROP TABLE IF EXISTS `matches`;
DROP TABLE IF EXISTS `article_comments`;
DROP TABLE IF EXISTS `activity_registrations`;
DROP TABLE IF EXISTS `activities`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `downloads`;
DROP TABLE IF EXISTS `organization_members`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `competition_types`;
DROP TABLE IF EXISTS `organization_infos`;
DROP TABLE IF EXISTS `board_members`;
DROP TABLE IF EXISTS `facilities`;
DROP TABLE IF EXISTS `contact_infos`;
DROP TABLE IF EXISTS `membership_applications`;
DROP TABLE IF EXISTS `rules`;
DROP TABLE IF EXISTS `prices`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `migrations`;

SET FOREIGN_KEY_CHECKS = 1;
