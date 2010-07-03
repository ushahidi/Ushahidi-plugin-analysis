<?php
/**
 * Performs install/uninstall methods for the Analysis Plugin
 *
 * @package    Ushahidi
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Analysis_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required database tables for the analysis module
	 */
	public function run_install()
	{
		// Create the database tables
		// Include the table_prefix
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."analysis`
			(
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`incident_id` INT NOT NULL COMMENT 'incident_id of the new report that is created',
				`user_id` INT NOT NULL COMMENT 'user_id of the user that performed this assessment',
				`analysis_date` DATETIME NOT NULL,
				PRIMARY KEY (id)
			);");
			
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."analysis_incident`
			(
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`analysis_id` INT NOT NULL,
				`incident_id` INT NOT NULL COMMENT 'incident_id\'s of the child reports that belong to this analysis',
				PRIMARY KEY (id)
			);");	
	}

	/**
	 * Deletes the database tables for the analysis module
	 */
	public function uninstall()
	{
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."analysis;
			DROP TABLE ".Kohana::config('database.default.table_prefix')."analysis_incident;
			");
	}
}