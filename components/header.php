<header class="menu-container">
	<div class="menu-content">
		<div class="logo">
			<img id="logo" src="./public/images/logo-asb.svg" alt="A.S. BEUVRY LA FORÊT" title="A.S. BEUVRY LA FORÊT">
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
				<li>
					<a href="../index.php"><i class="fas fa-home"></i></a>
				</li>
				<li>
					<a href="#">Licenciés</a>
				</li>
				<li>
					<a href="#">Éducateurs</a>
				</li>
				<li>
					<a href="#">Statistiques</a>
				</li>
				<li>
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