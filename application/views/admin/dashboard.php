<div class="d-flex justify-content-between align-items-center mb-4">
	<h1>Painel Administrativo</h1>
	<a href="<?= base_url('autenticacao/sair') ?>" class="btn btn-outline-secondary">Sair</a>
</div>

<div class="alert alert-info">
	Olá, <strong><?= htmlspecialchars($user_name) ?></strong>! Você está no painel administrativo.
</div>

<div class="row">
	<div class="col-md-4">
		<div class="card text-white bg-primary mb-3">
			<div class="card-body">
				<h5 class="card-title">Usuários</h5>
				<p class="card-text display-6">-</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card text-white bg-success mb-3">
			<div class="card-body">
				<h5 class="card-title">Cursos</h5>
				<p class="card-text display-6">-</p>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card text-white bg-warning mb-3">
			<div class="card-body">
				<h5 class="card-title">Matrículas</h5>
				<p class="card-text display-6">-</p>
			</div>
		</div>
	</div>
</div>
