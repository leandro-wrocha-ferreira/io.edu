<div class="d-flex justify-content-between align-items-center mb-4">
	<h1>Painel do Aluno</h1>
	<a href="<?= base_url('autenticacao/sair') ?>" class="btn btn-outline-secondary">Sair</a>
</div>

<div class="alert alert-info">
	Olá, <strong><?= htmlspecialchars($user_name) ?></strong>! Bem-vindo à sua área.
</div>

<div class="row">
	<div class="col-md-6">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Meus Cursos</h5>
				<p class="card-text">Você ainda não está matriculado em nenhum curso.</p>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title">Próximas Aulas</h5>
				<p class="card-text">Nenhuma aula agendada.</p>
			</div>
		</div>
	</div>
</div>
