<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Analysis Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class analysis {
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Add a Sub-Nav Link
		Event::add('ushahidi_action.nav_admin_reports', array($this, '_report_link'));
		
		// Only add the events if we are on that controller
		if (Router::$current_uri == "admin/reports")
		{
			plugin::add_stylesheet('analysis/views/css/buttons');
			
			// Add Buttons to the report List
			Event::add('ushahidi_action.report_extra_admin', array($this, '_reports_list_buttons'));
		}
		elseif (Router::$controller == 'analysis')
		{
			plugin::add_javascript('analysis/views/js/ui.dialog');
			plugin::add_javascript('analysis/views/js/ui.draggable');
			plugin::add_javascript('analysis/views/js/ui.resizable');
			plugin::add_stylesheet('analysis/views/css/main');
		}
		elseif (strripos(Router::$current_uri, "admin/reports/edit") !== false)
		{
			plugin::add_stylesheet('analysis/views/css/report');
			plugin::add_javascript('analysis/views/js/jquery.copy.min');
			Event::add('ushahidi_action.report_pre_form_admin', array($this, '_reports_list_analysis'));
			Event::add('ushahidi_action.header_scripts_admin', array($this, '_save_analysis_js'));
			Event::add('ushahidi_action.report_edit', array($this, '_save_analysis'));
		}
	}
	
	public function _report_link()
	{
		$this_sub_page = Event::$data;
		echo ($this_sub_page == "analysis") ? "Analysis" : "<a href=\"".url::site()."admin/analysis\">Analysis</a>";
	}
	
	public function _reports_list_buttons()
	{		
		$incident = Event::$data;
		// Is this report an Assessment?
		$analysis = ORM::factory('analysis')
			->where('incident_id', $incident->id)
			->find();
		if ($analysis->loaded)
		{
			$button = View::factory('analysis/buttons');
			$button->incident_id = $incident->id;
			$button->render(TRUE);
		}
	}
	
	public function _reports_list_analysis()
	{
		$incident_id = Event::$data;
		
		// Load Saved Analysis
		$analysis = ORM::factory('analysis')
			->where('incident_id', $incident_id)
			->find();
		if ($analysis->loaded)
		{
			// Find Attached Incidents
			$related = ORM::factory('analysis_incident')
				->where('analysis_id', $analysis->id)
				->find_all();
			
			$a_ids = array();
			foreach ($related as $a_id)
			{
				$a_ids[] = $a_id->incident_id;
			}
			$report_list = View::factory('analysis/report_list');
			$report_list->a_ids = $a_ids;
			$report_list->render(TRUE);
		}
		else
		{
			if (isset($_GET['a_id']))
			{
				$report_list = View::factory('analysis/report_list');
				$report_list->a_ids = $_GET['a_id'];
				$report_list->render(TRUE);
			}
		}
	}
	
	public function _save_analysis()
	{
		$incident = Event::$data;
		if (isset($_POST['a_id']) AND !empty($_POST['a_id']))
		{
			$a_ids = $_POST['a_id'];
			$analysis = ORM::factory('analysis')
				->where('incident_id', $incident->id)
				->find();

			// Save Analysis
			$analysis->incident_id = $incident->id;
			$analysis->user_id = $_SESSION['auth_user']->id;
			$analysis->analysis_date = date("Y-m-d H:i:s",time());
			$analysis->save();

			// Delete Reports associated with this Analysis (if any)
			ORM::factory('analysis_incident')
				->where('analysis_id', $analysis->id)
				->delete_all();

			// Save Associated Reports
			foreach ($a_ids as $a_id)
			{
				$analysis_incident = ORM::factory('analysis_incident');
				$analysis_incident->analysis_id = $analysis->id;
				$analysis_incident->incident_id = $a_id;
				$analysis_incident->save();

				// Deactivate reports associated with this analysis
				$deactivated = ORM::factory('incident')->find($a_id);
				if ($deactivated->loaded)
				{
					$deactivated->incident_active = 0;
					$deactivated->save();
				}
			}
		}		
	}
	
	public function _save_analysis_js()
	{
		View::factory('analysis/report_list_js')->render(TRUE);
	}
}

new analysis;