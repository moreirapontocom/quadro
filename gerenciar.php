<?php 
$message = '';
$warning = '';

if ( isset( $_POST['quadro'] ) ) {
		
	$avisos = new quadro();

	$button = $_POST['button'];
    if ( $button == "Criar" ) {

		if ( isset( $_POST['tituloAviso'] ) && isset( $_POST['conteudoAviso'] ) && isset( $_POST['paraAviso'] ) ) {
			$titulo = $_POST['tituloAviso'];
			$conteudo = $_POST['conteudoAviso'];
			$para = $_POST['paraAviso'];
			
			$cria = $avisos->cria_campanha($titulo,$conteudo,$para);
			if ( $cria )
		        $message = array('updated','Campanha criada');
		    else
		        $message = array('error','Erro ao criar a campanha');
		    
		    $warning = '<div class="'.$message[0].'" id="message"><p>'.$message[1].'</p></div>';
		}

	} // end button Criar
	
	elseif ( $button == "Apagar a campanha selecionada" ) {
		
		if ( isset( $_POST['campanha'] ) ) {
			$campanha = $_POST['campanha'];
			
			$apaga = $avisos->apaga_campanha($campanha);
			if ( $apaga )
		        $message = array('updated','Campanha apagada');
		    else
		        $message = array('error','Erro ao apagar a campanha');
		    
		    $warning = '<div class="'.$message[0].'" id="message"><p>'.$message[1].'</p></div>';
		}
		
	} // end button Apagar a campanha selecionada
	
	elseif ( $button == "Ativar a campanha selecionada" ) {
		
		if ( isset( $_POST['campanha'] ) ) {
			$campanha = $_POST['campanha'];
			
			$ativa = $avisos->ativa_campanha($campanha);
			if ( $ativa )
		        $message = array('updated','Campanha ativada');
		    else
		        $message = array('error','Erro ao ativar a campanha');
		    
		    $warning = '<div class="'.$message[0].'" id="message"><p>'.$message[1].'</p></div>';
		}
		
	} // end button Ativar a campanha selecionada

	elseif ( $button == "Desativar campanhas" ) {
		
		$desativa = $avisos->desativa_campanhas();
		if ( $desativa )
	        $message = array('updated','Todas as campanhas foram desativadas');
	    else
	        $message = array('error','Erro ao desativar todas as campanhas');
	    
	    $warning = '<div class="'.$message[0].'" id="message"><p>'.$message[1].'</p></div>';
		
	} // end button Desativar campanhas

}
?>

<div class="wrap">
    <div id="icon-plugins" class="icon32"><br /></div> 
    <h2>Quadro de avisos</h2>
    
    <?php echo $warning; ?>
    
    <h3>Crie um aviso</h3>
    <p>
	    <form action="<?php $PHP_SELF; ?>" method="post">
	    	<input type="hidden" name="quadro" value="1" />
	    	<label for="tituloAviso">Título do aviso</label><input type="text" name="tituloAviso" id="tituloAviso" />
	    	<textarea name="conteudoAviso" cols="" rows=""></textarea>
	    	<br />
	    	<select name="paraAviso">
	    		<option value="1">1</option>
	    		<option value="2">2</option>
	    		<option value="3">3</option>
	    		<option value="4">4</option>
	    	</select>
	    	<ul>
	    	<?php
			    $listagem_sites = new quadro();
        		$results_sites = $listagem_sites->lista_sites();
			    foreach ( $results_sites as $itens ) {
			        $blog_details = get_blog_details($itens->blog_id);
			        echo '<li>lista 1<a href="'. $blog_details->siteurl .'">' . $blog_details->blogname .'</a></li>';
			        //echo '<li>teste</li>';
			    }
			?>
			</ul>
	    	<br />
	    	<input type="submit" name="button" class="button-primary" value="Criar" />
	    </form>
    </p>
    
    <br />
    
    <div class="clear"></div>
    
    <hr />
    
    <br />
    
    <h3>Desative todas as campanhas</h3>
    <small>Desative todas as campanhas para não exibir nenhuma mensagem no dashboard dos sites da rede.</small>
    <p>
    	<form action="<?php $PHP_SELF; ?>" method="post">
			<input type="hidden" name="quadro" value="1" />
    		<input type="submit" name="button" class="button-primary" value="Desativar campanhas" />
		</form>
    </p>
    
    <br />
    
    <hr />
    
    <br />
    
    <h3>Gerencie as campanhas</h3>
    
    <p>
    	<?php
    	$listagem_campanhas = new quadro();
        $results_campanhas = $listagem_campanhas->lista_campanhas();
		
		if ( $results_campanhas == 0 ) {
			?>
			Nenhuma campanha encontrada.<br />
			Crie sua primeira campanha! Ao criar uma campanha, ela será automaticamente definida como <i>Inativa</i>.
			<?php 
		} else {
	    	?>
	    	<form action="<?php $PHP_SELF; ?>" method="post">
	    		<input type="hidden" name="quadro" value="1" />
	    		<ul>
	    			<li>
		    			<table border="0" cellpadding="5" cellspacing="0" width="100%">
		    				<tr>
		    					<td style="width: 30px;">&nbsp;</td>
		    					<td><b>Título da campanha</b></td>
		    					<td style="width: 200px;"><b>Quem vê</b></td>
		    				</tr>
		    			</table>
	    			</li>
	    			
		            <?php
		            $i = 0;
		            foreach ( $results_campanhas as $itens ) {
		            	( $i % 2 == 0 ) ? $cor = '#EFEFEF' : $cor = '#FFF';
		            	?>
		            	<li style="background-color: <?php echo $cor; ?>;">
		            		<table border="0" cellpadding="5" cellspacing="0" width="100%">
			    				<tr>
			    					<td style="width: 30px;"><input type="radio" name="campanha" <?php if ( $itens->ativo == '1' ) { echo 'checked="checked"'; } ?> value="<?php echo $itens->id; ?>" id="item_<?php echo $itens->id; ?>" /></td>
			    					<td><label for="item_<?php echo $itens->id; ?>"><?php echo $itens->titulo; ?></label></td>
			    					<td style="width: 200px;">Quem vê</td>
			    				</tr>
			    			</table>
		                </li>
		                <?php
		                $i++;
		            }
					?>
				</ul>
				<input type="submit" name="button" class="button-primary" value="Ativar a campanha selecionada" />
				<input type="submit" name="button" class="button-primary" value="Apagar a campanha selecionada" style="float: right;" />
			</form>
			<?php
		}
		?>
    </p>
    
</div>