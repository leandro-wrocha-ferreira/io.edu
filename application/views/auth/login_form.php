<div class="mb-4">
	<h2 class="h3 fw-bold text-edu-heading mb-2">Entrar na plataforma</h2>
	<p class="text-edu-muted fs-6 mb-0">
		Informe seu e-mail e senha cadastrados para acessar seus cursos e trilhas.
	</p>
</div>

<!-- Feedback Alerts -->
<?php if ($this->session->flashdata('error')): ?>
	<div class="edu-alert edu-alert-danger alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill edu-alert-icon" aria-hidden="true"></i>
		<div class="edu-alert-body"><?= html_escape($this->session->flashdata('error')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<?php if ($this->session->flashdata('success')): ?>
	<div class="edu-alert edu-alert-success alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-check-circle-fill edu-alert-icon" aria-hidden="true"></i>
		<div class="edu-alert-body"><?= html_escape($this->session->flashdata('success')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<?php if (validation_errors()): ?>
	<div class="edu-alert edu-alert-danger alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill edu-alert-icon" aria-hidden="true"></i>
		<div class="edu-alert-body"><?= validation_errors() ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<!-- Login Form -->
<?= form_open('entrar', ['id' => 'form-login', 'class' => 'needs-validation', 'novalidate' => 'novalidate']) ?>
	<div class="edu-form-group">
		<label for="email" class="edu-form-label">
			E-mail <span class="text-danger" aria-hidden="true">*</span>
		</label>
		<div class="position-relative">
			<input type="email"
			       class="edu-form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
			       id="email"
			       name="email"
			       value="<?= set_value('email') ?>"
			       autocomplete="email"
			       spellcheck="false"
			       placeholder="seu.email@dominio.com"
			       required
			       autofocus>
		</div>
		<?php if (form_error('email')): ?>
			<div class="invalid-feedback"><?= form_error('email') ?></div>
		<?php endif; ?>
	</div>

	<div class="edu-form-group">
		<div class="d-flex align-items-center justify-content-between mb-1">
			<label for="password" class="edu-form-label mb-0">
				Senha <span class="text-danger" aria-hidden="true">*</span>
			</label>
			<a href="#" class="fs-7 text-edu-primary fw-medium text-decoration-none">
				Esqueceu a senha?
			</a>
		</div>
		<input type="password"
		       class="edu-form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
		       id="password"
		       name="password"
		       autocomplete="current-password"
		       placeholder="••••••••"
		       required>
		<?php if (form_error('password')): ?>
			<div class="invalid-feedback"><?= form_error('password') ?></div>
		<?php endif; ?>
	</div>

	<div class="d-flex align-items-center justify-content-between mb-4">
		<div class="edu-form-check">
			<input class="edu-form-check-input" type="checkbox" id="remember" name="remember" value="1">
			<label class="form-check-label fs-7" for="remember">
				Lembrar meu acesso
			</label>
		</div>
	</div>

	<button type="submit" class="edu-btn edu-btn-primary edu-btn-lg w-100" id="btn-submit-login">
		<i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
		Acessar Minha Conta
	</button>
<?= form_close() ?>
