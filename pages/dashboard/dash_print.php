<!-- Content Row -->
<div class="row">
	<!-- Area Chart -->
	<div class="col-xl-8 col-lg-7">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="timeline-tab" data-toggle="tab" data-target="#timeline" type="button" role="tab" aria-controls="timeline" aria-selected="true"><i class="mdi mdi-chart-timeline-variant"></i> Timeline</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="rede-tab" data-toggle="tab" data-target="#rede" type="button" role="tab" aria-controls="rede" aria-selected="false"><i class="mdi mdi-printer-pos-network-outline"></i> Rede</button>
			</li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="timeline" role="tabpanel" aria-labelledby="timeline-tab" tabindex="0">
				<div class="card shadow pb-3 mb-4">
					<!-- Card Body -->
					<div class="card-body">
						<h6 class="mb-3 font-weight-bold text-warning">Quantos recursos foram gastos nos últimos <?php echo (isset($_SESSION['f1']) && $_SESSION['f1'] == 'M' ? '6 meses' : '7 dias'); ?>?</h6>
						<div class="row">
							<div class="col-12">
								<label class="mr-2">Filtrar por:</label>
								<select id="filtro-graf-imp">
									<option value="D" <?php if(isset($_SESSION['f1']) && $_SESSION['f1'] == 'D'){ echo 'selected'; } ?>>Últimos 7 dias</option>
									<option value="M" <?php if(isset($_SESSION['f1']) && $_SESSION['f1'] == 'M'){ echo 'selected'; } ?>>Últimos 6 meses</option>
								</select>
							</div>
							<div class="col-12">
								<div class="chart-area" id="graf-imp-dia"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="rede" role="tabpanel" aria-labelledby="rede-tab" tabindex="0">
				<div class="card shadow mb-4">
					<!-- Card Body -->
					<div class="card-body">
						<h6 class="mb-3 font-weight-bold text-warning">Quais foram os custos que tive em cada impressora?</h6>
						<div class="row">
							<div class="col-12">
								<label class="mr-2">Filtrar por:</label>
								<select id="filtro-graf-custos-imp">
									<option value="-1" <?php if(isset($_SESSION['f3']) && $_SESSION['f3'] == '-1'){ echo 'selected'; } ?>>Desde o início</option>
									<option value="7"  <?php if(isset($_SESSION['f3']) && $_SESSION['f3'] == '7'){ echo 'selected'; } ?>>Últimos 7 dias</option>
									<option value="30" <?php if(isset($_SESSION['f3']) && $_SESSION['f3'] == '30'){ echo 'selected'; } ?>>Últimos 30 dias</option>
									<option value="90" <?php if(isset($_SESSION['f3']) && $_SESSION['f3'] == '90'){ echo 'selected'; } ?>>Últimos 3 meses</option>
								</select>
							</div>
							<div class="col-12">
								<div class="chart-area" id="graf-imp-custos"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Pie Chart -->
	<div class="col-xl-4 col-lg-5">
		<div class="card shadow mb-4">
			<!-- Card Body -->
			<div class="card-body">
				<h6 class="m-0 font-weight-bold text-warning">Quais impressoras foram mais utilizadas?</h6>
				<div id="graf-uso-imp"></div>
			</div>
		</div>
	</div>
</div>
<!-- Content Row -->
<div class="row">
	<div class="col-12">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" data-toggle="tab" data-target="#aba-visao-geral" type="button" role="tab"><i class="mdi mdi-home-variant-outline"></i> Home</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" data-toggle="tab" data-target="#aba-detalhes" type="button" role="tab"><i class="mdi mdi-information-slab-circle-outline"></i> Detalhes</button>
			</li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="aba-visao-geral" role="tabpanel" tabindex="0">
				<div class="card shadow mb-4">
					<div class="card-body">
						<div class="mb-4">
							<h5 class="text-warning"><i class="mdi mdi-home-variant-outline"></i> Visão Geral</h5>
							<h6 class="text-gray">Visão geral de minhas impressoras assim como níveis de suprimentos</h6>
						</div>
						<div id="monit-impressoras"></div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="aba-detalhes" role="tabpanel" tabindex="0">
				<div class="card shadow mb-4">
					<div class="card-body" id="tabela-trab-imp">
						<div class="mb-4">
							<h5 class="text-warning"><i class="mdi mdi-information-slab-circle-outline"></i> Dispositivos - Resumo/Detalhes</h5>
							<h6 class="text-gray">Registro detalhado ou simplificado do trabalho de minhas impressoras</h6>
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
													<label class="form-label">Dispositivo:</label>
													<select class="form-control" name="dispositivo">
													<option value="-1">---- Todos ----</option>
													<?php
													$sql_impressoras = mysqli_query($con, "SELECT * FROM ad_impressoras ORDER BY nome ASC");
													while($result_imp = mysqli_fetch_array($sql_impressoras)){
														if(isset($_SESSION['filtro_imp']) && $_SESSION['filtro_imp'] == $result_imp['id']){
															$selected = 'selected';
														} else{
															$selected = '';
														}
														echo '<option value="'.$result_imp['id'].'" '.$selected.'>'.$result_imp['nome'].'</option>';
													}
													?>
													</select>
												</div>
											</div>
											<div class="col-sm-12 col-md-6">
												<div class="mb-3">
													<label class="form-label">Modelo:</label>
													<select class="form-control" name="modelo">
													<option value="-1">---- Todos ----</option>
													<?php
													$sql_modelos_imp = mysqli_query($con,"SELECT * FROM imp_modelos");
													while($modelos_imp = mysqli_fetch_array($sql_modelos_imp)){
														if(isset($_SESSION['filtro_imp_md']) && $_SESSION['filtro_imp_md'] == $modelos_imp['id']){
															$selected = 'selected';
														} else{
															$selected = '';
														}
														echo '<option value="'.$modelos_imp['id'].'" '.$selected.'>'.$modelos_imp['marca'].'</option>';
													}
													?>
													</select>
												</div>
											</div>
											<div class="col-sm-12 col-md-6">
												<label class="form-label">Data:</label>
												<input type="text" class="form-control date-range" name="data" placeholder="Clique aqui..." value="<?php if(isset($_SESSION['filtro_imp_data'])){ echo $_SESSION['filtro_imp_data']; } ?>">
											</div>
											<div class="col-sm-12 col-md-6 d-flex align-items-end">
												<button type="submit" class="btn btn-success mr-3" name="filtro_imp"><i class="fas fa-check"></i> Aplicar</button>
												<button type="submit" class="btn btn-danger" name="limpar_filtro_imp"><i class="fas fa-trash"></i> Limpar</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!-- Tabela -->
						<div class="table-responsive">
							<table class="table table-bordered" id="<?php echo $tabela3; ?>" width="100%" cellspacing="0" style="margin-top:30px !important; margin-bottom:30px !important;">
								<thead>
									<tr>
										<th>Dispositivo</th>
										<th>Modelo</th>
										<th>Páginas</th>
										<th>Tamanho</th>
										<th>Dt. Enviado</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Dispositivo</th>
										<th>Modelo</th>
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