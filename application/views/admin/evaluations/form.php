<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<a href="<?= base_url('admin/avaliacoes') ?>" class="text-muted text-decoration-none">Avaliações</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body"><?= !empty($evaluation) ? 'Editar Avaliação' : 'Nova Avaliação' ?></span>
			</nav>
			<h1 class="edu-page-header-title"><?= !empty($evaluation) ? html_escape($evaluation['title']) : 'Criar Nova Avaliação' ?></h1>
			<p class="edu-page-header-desc">Monte questionários objetivos, provas modulares e instrumentos com correção automatizada.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/avaliacoes') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left" aria-hidden="true"></i>
				<span>Voltar para Listagem</span>
			</a>
		</div>
	</header>

	<form id="evaluation-form" onsubmit="event.preventDefault(); alert('Protótipo Visual: Nenhuma alteração foi persistida no banco.');" class="animate-fade-up animate-delay-1">
		<div class="edu-form-card mb-4">
			<!-- Section 1: Parameters -->
			<div class="edu-form-section">
				<h2 class="edu-form-section-title">Parâmetros da Avaliação</h2>
				<p class="edu-form-section-desc">Título, vinculação curricular, tempo limite e regras de tentativa.</p>

				<div class="row g-3">
					<div class="col-md-8 edu-form-field">
						<label for="eval-title" class="edu-form-label">
							Título da Avaliação <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="text"
						       id="eval-title"
						       name="title"
						       class="form-control"
						       value="<?= html_escape($evaluation['title'] ?? 'Avaliação Final — Módulo 1') ?>"
						       required>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="eval-course" class="edu-form-label">Curso Vinculado <span class="text-danger" aria-hidden="true">*</span></label>
						<select id="eval-course" name="course_id" class="form-select">
							<?php foreach ($courses as $course_item): ?>
								<option value="<?= (int) $course_item['id'] ?>"><?= html_escape($course_item['title']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="eval-time" class="edu-form-label">Tempo Limite de Prova</label>
						<div class="input-group">
							<input type="number" id="eval-time" class="form-control" value="45" min="5">
							<span class="input-group-text">minutos</span>
						</div>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="eval-attempts" class="edu-form-label">Tentativas Permitidas</label>
						<select id="eval-attempts" class="form-select">
							<option value="1">1 tentativa única</option>
							<option value="2" selected>2 tentativas</option>
							<option value="3">3 tentativas</option>
							<option value="99">Ilimitadas (Simulado)</option>
						</select>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="eval-grade" class="edu-form-label">Nota Mínima para Aprovação</label>
						<input type="text" id="eval-grade" class="form-control" value="7,0">
					</div>
				</div>
			</div>

			<!-- Section 2: Questions Authoring -->
			<div class="edu-form-section">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<div>
						<h2 class="edu-form-section-title mb-0">Banco de Questões da Prova</h2>
						<p class="edu-form-section-desc mb-0">Cadastre os enunciados e assinale a alternativa correta do gabarito.</p>
					</div>
					<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="alert('Protótipo: Nova questão simulada.');">
						<i class="bi bi-plus-lg me-1"></i> Adicionar Questão
					</button>
				</div>

				<!-- Question Block 1 -->
				<div class="border rounded-3 p-4 mb-3 bg-subtle">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="badge bg-primary text-white fw-semibold">Questão 01</span>
						<div class="edu-action-group">
							<button type="button" class="edu-action-btn" title="Duplicar"><i class="bi bi-copy"></i></button>
							<button type="button" class="edu-action-btn edu-action-btn-delete" title="Excluir"><i class="bi bi-trash"></i></button>
						</div>
					</div>

					<div class="mb-3">
						<label class="edu-form-label">Enunciado da Questão</label>
						<textarea class="form-control" rows="2">Qual é o objetivo central da aplicação de Design Tokens em plataformas educacionais White-Label?</textarea>
					</div>

					<div class="d-flex flex-column gap-2">
						<div class="input-group">
							<div class="input-group-text bg-white">
								<input class="form-check-input mt-0" type="radio" name="q1_correct" checked aria-label="Gabarito alternativa A">
							</div>
							<span class="input-group-text fw-bold">A</span>
							<input type="text" class="form-control" value="Centralizar cores, tipografia e raios em variáveis semânticas desacopladas de código duro.">
						</div>

						<div class="input-group">
							<div class="input-group-text bg-white">
								<input class="form-check-input mt-0" type="radio" name="q1_correct" aria-label="Gabarito alternativa B">
							</div>
							<span class="input-group-text fw-bold">B</span>
							<input type="text" class="form-control" value="Substituir todo o código do backend em CodeIgniter por Node.js.">
						</div>

						<div class="input-group">
							<div class="input-group-text bg-white">
								<input class="form-check-input mt-0" type="radio" name="q1_correct" aria-label="Gabarito alternativa C">
							</div>
							<span class="input-group-text fw-bold">C</span>
							<input type="text" class="form-control" value="Apenas criar arquivos de imagem em alta definição.">
						</div>
					</div>
				</div>

				<div class="text-center py-2">
					<button type="button" class="edu-btn edu-btn-ghost w-100 border border-dashed py-3" onclick="alert('Protótipo: Nova questão simulada.');">
						<i class="bi bi-plus-circle me-1"></i> Clique aqui para adicionar outra questão
					</button>
				</div>
			</div>

			<!-- Actions Footer -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/avaliacoes') ?>" class="edu-btn edu-btn-outline">
					Cancelar
				</a>
				<button type="submit" class="edu-btn edu-btn-primary">
					<i class="bi bi-floppy me-1" aria-hidden="true"></i>
					<span>Salvar Avaliação</span>
				</button>
			</div>
		</div>
	</form>
</div>
