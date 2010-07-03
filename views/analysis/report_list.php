<div class="analysis-assessment-list">
	<h4>Assessment For the Following Reports:</h4>
	<ul>
	<?php
	foreach ($a_ids as $a_id)
	{
		$analysis_incident = ORM::factory('incident')->find($a_id);
		if ($analysis_incident->loaded)
		{
			echo "<li>";
			echo "<a href=\"".url::site()."admin/reports/edit/".$a_id."\" target=\"_blank\">".$analysis_incident->incident_title."</a> <span class=\"analysis-mapme\">[<a href=\"javascript:analysisMapme('".$analysis_incident->location->longitude."','".$analysis_incident->location->latitude."')\">map</a>]</span>";
			echo "<input type=\"hidden\" name=\"a_id[]\" value=\"".$analysis_incident->id."\">";
			echo "</li>";
		}
	}
	?>
	</ul>
</div>