<script type="text/javascript" charset="utf-8">
function analysisMapme(lon, lat) {
	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
	var myPoint = new OpenLayers.LonLat(lon, lat);
	m = new OpenLayers.Marker(myPoint.transform(proj_4326, proj_900913));
	markers.clearMarkers();
	markers.addMarker(m);
	
	// Update Form Value
	$("#latitude").attr("value", lat);
	$("#longitude").attr("value", lon);
}
$(document).ready(function() {
	$('a#analysis_toggle').click(function() {
		$('#analysis_report_details').toggle(400);
		return false;
	});
});
</script>