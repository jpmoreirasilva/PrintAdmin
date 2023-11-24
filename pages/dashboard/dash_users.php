<!-- Content Row -->
<div class="row">
	<!-- Area Chart -->
	<div class="col-xl-8 col-lg-7">
		<div class="card shadow mb-4">
			<!-- Card Body -->
			<div class="card-body">
				<h6 class="mb-3 font-weight-bold text-warning">Quais foram os custos que tive em cada setor?</h6>
				<div class="row">
					<div class="col-12">
						<label class="mr-2">Filtrar por:</label>
						<select id="filtro-graf-setor">
							<option value="-1" <?php if(isset($_SESSION['f2']) && $_SESSION['f2'] == '-1'){ echo 'selected'; } ?>>Desde o início</option>
							<option value="7"  <?php if(isset($_SESSION['f2']) && $_SESSION['f2'] == '7'){ echo 'selected'; } ?>>Últimos 7 dias</option>
							<option value="30" <?php if(isset($_SESSION['f2']) && $_SESSION['f2'] == '30'){ echo 'selected'; } ?>>Últimos 30 dias</option>
							<option value="90" <?php if(isset($_SESSION['f2']) && $_SESSION['f2'] == '90'){ echo 'selected'; } ?>>Últimos 3 meses</option>
						</select>
					</div>
					<div class="col-12">
						<div class="chart-area" id="graf-imp-setor"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Pie Chart -->
	<div class="col-xl-4 col-lg-5">
		<div class="tab-pane" id="tab-pane-7" role="tabpanel" aria-labelledby="nav-tab-7" tabindex="0">
			<div class="card shadow mb-4">
				<!-- Card Body -->
				<div class="card-body">
					<h6 class="mb-3 font-weight-bold text-warning">Quais usuários mais utilizaram meus recursos?</h6>
					<div id="rank-usuarios"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Content Row -->
<div class="row">
	<div class="col-12" id="tabela-ope-info">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="nav-tab-3" data-toggle="tab" data-target="#tab-pane-3" type="button" role="tab" aria-controls="tab-pane-1" aria-selected="true"><i class="mdi mdi-chart-gantt" aria-hidden="true"></i> Resumo</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="nav-tab-4" data-toggle="tab" data-target="#tab-pane-4" type="button" role="tab" aria-controls="tab-pane-4" aria-selected="true"><i class="mdi mdi-information-slab-circle-outline" aria-hidden="true"></i> Detalhes</button>
			</li>
		</ul>
		<div class="card shadow mb-4">
			<div class="card-body">
				<div class="mb-3">
					<h5 class="text-warning"><i class="mdi mdi-information-slab-circle-outline"></i> Operações - Resumo/Detalhes</h5>
					<h6 class="text-gray">Registro detalhado ou simplificado do trabalho de minhas impressoras por usuário e/ou setor.</h6>
				</div>
				<!-- Filtros -->
				<div class="d-block w-100 mb-3">
					<button class="btn btn-primary mb-3" type="button" data-toggle="collapse" data-target="#filtros" aria-expanded="false" aria-controls="filtros">
						<i class="fas fa-filter"></i> Filtros
					</button>
					<div class="collapse" id="filtros">
						<div class="card card-body">
							<form method="POST" enctype="multipart/form-data">
								<div class="row">
									<div class="col-sm-12 col-md-6">
										<div class="mb-3">
											<label class="form-label">Usuário:</label>
											<select class="form-control" name="usuario">
											<option value="-1">---- Todos ----</option>
											<?php
											$sql_usuarios = mysqli_query($con, "SELECT * FROM ad_usuarios ORDER BY nome ASC");
											while($usuario = mysqli_fetch_array($sql_usuarios)){
												if(isset($_SESSION['filtro_usuario']) && $_SESSION['filtro_usuario'] == $usuario['id']){
													$selected = 'selected';
												} else{
													$selected = '';
												}
												echo '<option value="'.$usuario['id'].'" '.$selected.'>'.$usuario['nome'].'</option>';
											}
											?>
											</select>
										</div>
									</div>
									<div class="col-sm-12 col-md-6">
										<div class="mb-3">
											<label class="form-label">Setor:</label>
											<select class="form-control" name="setor">
											<option value="-1">---- Todos ----</option>
											<?php
											$sql_grupos = mysqli_query($con, "SELECT * FROM ad_grupos_info ORDER BY grupo ASC");
											while($grupo = mysqli_fetch_array($sql_grupos)){
												if(isset($_SESSION['filtro_setor']) && $_SESSION['filtro_setor'] == $grupo['id']){
													$selected = 'selected';
												} else{
													$selected = '';
												}
												echo '<option value="'.$grupo['id'].'" '.$selected.'>'.$grupo['grupo'].'</option>';
											}
											?>
											</select>
										</div>
									</div>
									<div class="col-sm-12 col-md-6">
										<label class="form-label">Data:</label>
										<input type="text" class="form-control date-range" name="data" placeholder="Clique aqui..." value="<?php if(isset($_SESSION['filtro_users_data'])){ echo $_SESSION['filtro_users_data']; } ?>">
									</div>
									<div class="col-sm-12 col-md-6 d-flex align-items-end">
										<button type="submit" class="btn btn-success mr-3" name="filtro_users"><i class="fas fa-check"></i> Aplicar</button>
										<button type="submit" class="btn btn-danger" name="limpar_filtro_users"><i class="fas fa-trash"></i> Limpar</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Tabelas -->
				<div class="tab-content">
					<div class="tab-pane active" id="tab-pane-3" role="tabpanel" aria-labelledby="nav-tab-3" tabindex="0">
						<!-- DataTales Example -->
						<div class="table-responsive">
							<table class="table table-bordered" id="<?php echo $tabela; ?>" width="100%" cellspacing="0" style="margin-top:30px !important; margin-bottom:30px !important;">
								<thead>
									<tr>
										<th>Usuário</th>
										<th>Dispositivo</th>
										<th>Páginas</th>
										<th>Tamanho</th>
										<th>Dt. Enviado</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Usuário</th>
										<th>Dispositivo</th>
										<th>Páginas</th>
										<th>Tamanho</th>
										<th>Dt. Enviado</th>
									</tr>
								</tfoot>
								<tbody></tbody>
							</table>
						</div>
					</div>
					<div class="tab-pane" id="tab-pane-4" role="tabpanel" aria-labelledby="nav-tab-4" tabindex="0">
						<!-- DataTales Example -->
						<div class="table-responsive">
							<table class="table table-bordered" id="<?php echo $tabela2; ?>" width="100%" cellspacing="0" style="margin-top:30px !important; margin-bottom:30px !important;">
								<thead>
									<tr>
										<th>Usuário</th>
										<th>Dispositivo</th>
										<th>Documento</th>
										<th>Páginas</th>
										<th>Tamanho</th>
										<th>Dt. Enviado</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Usuário</th>
										<th>Dispositivo</th>
										<th>Documento</th>
										<th>Páginas</th>
										<th>Tamanho</th>
										<th>Dt. Enviado</th>
									</tr>
								</tfoot>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>