<!doctype html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>Mapa Hrvatske</title>
	<style>
		* { font-family: Arial; box-sizing: border-box;}
		.page-wrapper { max-width: 1280px; margin: auto; }
		header, footer, .content { overflow: auto; }
		.main, .extras { float: left; padding: 8px;}
		.main { width: 66.666%; }
		.extras { width: 33.333%; clear:  right;}
		.hidden { display: none; }
		.svg-map { width: 100%; height: 100vh; }
	</style>
	<script>
	</script>

	<?php include 'map.php';?>
</head>
<body>
	<div class="page-wrapper">
		<header>
			<h1>Mapa Hrvatske</h1>
		</header>
		<div class="content">
			<div class="main">
				<?php 
					if (isset($_GET['d'])) {
						$date = $_GET['d'];
					} else {
						$date = date("Ymd");
					}

					if (!preg_match("/^((((19|[2-9]\d)\d{2})(0[13578]|1[02])(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})(0[13456789]|1[012])(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})02(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))0229))$/",$date))
					{
						$date = '';
						header("HTTP/1.0 404 Not Found");
						echo "<h2>Neispravan format datuma!</h2>";
						die();
					}

					echo "<h2>Datum: " . date("d.m.Y", strtotime($date)) . "</h2>";
				?>
			</div>
			<div class="svg-map-container">
				<?php 
					echo "<svg class=\"svg-map\">";

					$counties = getMap();
					while($row = $counties->fetch_array())
					{
						$color = getCountyColor($date, $row["county_id"]);
						echo $color;
						echo "<path d=\"" . $row["svg_path"] . "\" style=\"fill: " . $color . "\" stroke=\"#FE6565\" stroke-opacity=\"1\" strokeWidth=\"0.25\"/>";
					}

					echo "</svg>";
				?>
			</div>
		</div>
	</div>
</body>