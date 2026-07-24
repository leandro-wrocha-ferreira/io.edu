<div class="row justify-content-center">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0">Login</h4>
			</div>
			<div class="card-body">
				<?php if ($this->session->flashdata('error')): ?>
					<div class="alert alert-danger">
						<?= $this->session->flashdata('error') ?>
					</div>
				<?php endif; ?>

				<?php if ($this->session->flashdata('success')): ?>
					<div class="alert alert-success">
						<?= $this->session->flashdata('success') ?>
					</div>
				<?php endif; ?>

				<?= form_open('autenticacao/login') ?>
					<div class="mb-3">
						<label for="email" class="form-label">Email</label>
						<input type="email" class="form-control" id="email" name="email" required autofocus>
					</div>
					<div class="mb-3">
						<label for="password" class="form-label">Senha</label>
						<input type="password" class="form-control" id="password" name="password" required>
					</div>
					<button type="submit" class="btn btn-primary w-100">Entrar</button>
				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>
