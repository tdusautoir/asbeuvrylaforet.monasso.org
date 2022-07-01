<?php

require_once("./db.php");
require_once("./function.php");

$currentpath = pathinfo($_SERVER['SCRIPT_NAME']);

if ($currentpath['basename'] === "index.php" || $currentpath['filename'] === "index") {
	$link_active[0] = true;
} else if ($currentpath['basename'] === "licencies.php" || $currentpath['filename'] === "licencies") {
	$link_active[1] = true;
} else if ($currentpath['basename'] === "educateurs.php" || $currentpath['filename'] === "educateurs") {
	$link_active[2] = true;
} else if ($currentpath['basename'] === "page.php" || $currentpath['filename'] === "page") {
	$link_active[3] = true;
} else if ($currentpath['basename'] === "page.php" || $currentpath['filename'] === "page") {
	$link_active[4] = true;
}

$logo = $db->query("SELECT logoPath FROM settings ORDER BY id DESC LIMIT 1;");
$get_logo = $logo->fetch(PDO::FETCH_ASSOC);

?>
<header class="menu-container">
	<div class="menu-content">
		<div class="logo">
			<a href="../">
				<img id="logo" src="<?= $get_logo["logoPath"] ?>" alt=" A.S. BEUVRY LA FORÊT" title="A.S. BEUVRY LA FORÊT">
			</a>
		</div>
		<div class="menu-mob">
			<a class="menu-trigger type7" href="#">
				<span></span>
				<span></span>
				<span></span>
			</a>
			<p class="menu-txt">Menu</p>
		</div>
		<nav class="nav-menu">
			<ul class="menu">
				<li <?php if (isset($link_active[0])) : ?>class="active" <?php endif; ?>>
					<a href="./index.php"><i class="fas fa-home"></i></a>
				</li>
				<li <?php if (isset($link_active[1])) : ?>class="active" <?php endif; ?>>
					<a href="./licencies.php">Licenciés</a>
				</li>
				<?php if (is_admin()) : ?>
					<li <?php if (isset($link_active[2])) : ?>class="active" <?php endif; ?>>
						<a href="./educateurs.php">Éducateurs</a>
					</li>
				<?php endif; ?>
				<li <?php if (isset($link_active[3])) : ?>class="active" <?php endif; ?>>
					<a href="#">Statistiques</a>
				</li>
				<li <?php if (isset($link_active[4])) : ?>class="active" <?php endif; ?>>
					<a href="#">Suivi des cotisations</a>
				</li>
			</ul>
		</nav>
	</div>
</header>

<script type="text/javascript">
	document.querySelector('.menu-trigger').addEventListener('click', function() {
		document.querySelector('.menu-trigger').classList.toggle('active-7');
		document.querySelector('body').classList.toggle('open');
	});
</script>