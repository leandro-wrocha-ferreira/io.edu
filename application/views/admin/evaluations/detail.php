<div class="container-fluid px-0">
	<!-- Context Header -->
	<header class="edu-context-header animate-fade-up">
		<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-2" aria-label="Localização">
			<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<a href="<?= base_url('admin/avaliacoes') ?>" class="text-muted text-decoration-none">Avaliações</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<span class="fw-medium text-body"><?= html_escape($evaluation['title']) ?></span>
		</nav>

		<div class="edu-context-header-top">
			<div>
				<div class="d-flex align-items-center gap-2 mb-1">
					<h1 class="edu-page-header-title mb-0"><?= html_escape($evaluation['title']) ?></h1>
					<span class="edu-badge edu-badge-success">● Publicada</span>
				</div>
				<p class="edu-page-header-desc mb-0">
					Curso: <strong class="text-body"><?= html_escape($evaluation['course_title']) ?></strong> · <?= html_escape($evaluation['module_title']) ?>
				</p>
			</div>

			<div class="d-flex align-items-center gap-2">
				<a href="<?= base_url('admin/avaliacoes/' . $evaluation['id'] . '/editar') ?>" class="edu-btn edu-btn-primary">
					<i class="bi bi-pencil" aria-hidden="true"></i>
					<span>Editar Questões</span>
				</a>
			</div>
		</div>

		<!-- Evaluation KPIs -->
		<div class="row g-3 mt-1">
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Total de Questões</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $evaluation['questions_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Tentativas Concluídas</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $evaluation['attempts_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Média Geral de Notas</span>
					<span class="fs-5 fw-bold text-success"><?= html_escape($evaluation['average_score']) ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Tempo Limite</span>
					<span class="fs-5 fw-bold text-heading font-monospace"><?= html_escape($evaluation['time_limit']) ?></span>
				</div>
			</div>
		</div>
	</header>

	<!-- Questions List -->
	<div class="edu-card animate-fade-up animate-delay-1 mb-4">
		<div class="edu-card-header d-flex justify-content-between align-items-center">
			<h3 class="edu-card-title">Estrutura das Questões Cadastradas</h3>
			<span class="badge bg-primary-subtle text-primary border border-primary-subtle">
				Gabarito Automático Ativo
			</span>
		</div>
		<div class="edu-card-body p-4">
			<!-- Question 1 -->
			<div class="border rounded-3 p-3 mb-3 bg-subtle">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="badge bg-primary text-white fw-semibold">Questão 01</span>
					<span class="text-muted small">Valor: 1,0 ponto · Múltipla Escolha</span>
				</div>
				<p class="fw-semibold text-heading mb-3">
					Qual é a principal vantagem da arquitetura DDD-Lite em aplicações legadas PHP sem recriar todo o ecossistema?
				</p>
				<div class="d-flex flex-column gap-2 ms-2">
					<div class="d-flex align-items-center gap-2 text-muted small">
						<i class="bi bi-circle"></i> A) Substitui o framework MVC existente por microsserviços imediatamente.
					</div>
					<div class="d-flex align-items-center gap-2 fw-semibold text-success small">
						<i class="bi bi-check-circle-fill text-success"></i> B) Isola entidades e casos de uso sem depender diretamente de bibliotecas ou classes internas do framework. (Correta)
					</div>
					<div class="d-flex align-items-center gap-2 text-muted small">
						<i class="bi bi-circle"></i> C) Elimina a necessidade de validação de formulários no controlador.
					</div>
					<div class="d-flex align-items-center gap-2 text-muted small">
						<i class="bi bi-circle"></i> D) Obriga o uso de migrations apenas para alteração visual.
					</div>
				</div>
			</div>

			<!-- Question 2 -->
			<div class="border rounded-3 p-3 bg-subtle">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<span class="badge bg-primary text-white fw-semibold">Questão 02</span>
					<span class="text-muted small">Valor: 1,0 ponto · Múltipla Escolha</span>
				</div>
				<p class="fw-semibold text-heading mb-3">
					Para que serve o atributo <code>aria-describedby</code> em campos de formulário acessíveis segundo as diretrizes WCAG?
				</p>
				<div class="d-flex flex-column gap-2 ms-2">
					<div class="d-flex align-items-center gap-2 fw-semibold text-success small">
						<i class="bi bi-check-circle-fill text-success"></i> A) Associa textos de ajuda ou mensagens de validação diretamente ao controle de entrada para leitores de tela. (Correta)
					</div>
					<div class="d-flex align-items-center gap-2 text-muted small">
						<i class="bi bi-circle"></i> B) Apenas estiliza a borda do input quando o mouse passa por cima.
					</div>
					<div class="d-flex align-items-center gap-2 text-muted small">
						<i class="bi bi-circle"></i> C) Define se o campo aceita dados em minúsculas ou maiúsculas.
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
