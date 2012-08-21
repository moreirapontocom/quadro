<?php
/*
 * Plugin Name: Quadro de avisos (para WP Multisite)
 * Plugin URI: http://lucasmoreira.com.br/plugin-wordpress-quadro
 * Description: Coloque um quadro de avisos nos subsites da sua rede WP Multisite
 * Version: 1.0
 * Author: Lucas Moreira de Souza
 * Author URI: http://lucasmoreira.com.br
 *
 * Copyright 2012 Lucas Moreira de Souza <moreirapontocom at gmail dot com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

class quadro {

    private static $wpdb;
    private static $info;

    public function inicializar() {
        global $wpdb;

        quadro::$wpdb = $wpdb;
        quadro::$info['plugin_fpath'] = dirname(__FILE__);
    }

    public function ativar() {
		if (is_null(quadro::$wpdb))
			quadro::inicializar();

		$createTable = "CREATE TABLE ".quadro::$wpdb->prefix."quadro_avisos (
id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
titulo varchar(255) NOT NULL,
conteudo text NOT NULL,
para text NOT NULL,
datahora timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
ativo tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Exibe um mural de avisos - próprio para WP multisite'";
		quadro::$wpdb->query( quadro::$wpdb->prepare($createTable) );
   }

    public function desativar() {
        if (is_null(quadro::$wpdb))
            quadro::inicializar();
            
        $dropTable  = "DROP TABLE `".quadro::$wpdb->prefix."quadro_avisos`";
        quadro::$wpdb->query( quadro::$wpdb->prepare($dropTable) );
    }
	
	public function salva_log($log) {
		$arquivo = fopen("/tmp/log.txt", "a");
		$escreve = fwrite($arquivo, $log."\n");
		fclose($arquivo);
	}

    public function lista_campanhas($ativo='',$para='') {
		if ( is_null(quadro::$wpdb) )
            quadro::inicializar();
		
		$sqlConsulta = "SELECT * FROM ".quadro::$wpdb->prefix."quadro_avisos ";

		if ( $ativo == 1 ) {
			$sqlConsulta .= "WHERE ativo = 1 ";
		} else {
			$sqlConsulta .= "WHERE ativo = 1 OR ativo = 0 ";
		}

		if ( !isset($para) || empty($para) || $para == '' || $para == NULL ) {
			$sqlConsulta .= "";
		} elseif ( $para == 'todos' ) {
			$sqlConsulta .= "AND para = 'todos' ";
		} else {
			$sqlConsulta .= " ";
		}
		
		$sqlConsulta .= " ORDER BY id DESC";

		$results = quadro::$wpdb->get_results( quadro::$wpdb->prepare($sqlConsulta) );

		if ( count($results) > 0 ) $results = $results;
		else $results = 0;

		if ( $results ) return $results;
		else return false;
   }
   
   public function lista_sites() {
   		if ( is_null(quadro::$wpdb) )
            quadro::inicializar();
	
		$sqlLista = "SELECT blog_id FROM ".quadro::$wpdb->prefix."blogs 
		WHERE public = 1 
		AND archived = 0 
		AND spam = 0 
		AND deleted = 0 
		AND blog_id != 1 
		ORDER BY path ASC";
	    $results = quadro::$wpdb->get_results( quadro::$wpdb->prepare($sqlLista) );

	    if ( $results ) return true;
		else return false;
   }
   
   public function cria_campanha($titulo='',$conteudo='',$para='') {
		if ( is_null(quadro::$wpdb) )
            quadro::inicializar();
		
		$sqlCria = "INSERT INTO ".quadro::$wpdb->prefix."quadro_avisos (titulo,conteudo,para) VALUES ('".$titulo."','".$conteudo."','".$para."')";
		$results = quadro::$wpdb->query( quadro::$wpdb->prepare($sqlCria) );
		if ( $results ) return true;
		else return false;
   }
   
	public function apaga_campanha($id='') {
		if ( is_null(quadro::$wpdb) )
            quadro::inicializar();

		$id = trim($id);
		if ( !empty($id) && is_numeric($id) ) {
			$delete = "DELETE FROM ".quadro::$wpdb->prefix."quadro_avisos WHERE id = ".$id." LIMIT 1";
			$results = quadro::$wpdb->query( quadro::$wpdb->prepare($delete) );
			
			if ( $results ) return true;
			else return false;
		}
	}
	
	public function ativa_campanha($id='') {
		if ( is_null(quadro::$wpdb) )
	        quadro::inicializar();
	
		$id = trim($id);
		if ( !empty($id) && is_numeric($id) ) {
			$desativaTodas = "UPDATE ".quadro::$wpdb->prefix."quadro_avisos SET ativo = 0";
			$ativaSelecionada = "UPDATE ".quadro::$wpdb->prefix."quadro_avisos SET ativo = 1 WHERE id = ".$id;
			
			$desativa = quadro::$wpdb->query( quadro::$wpdb->prepare($desativaTodas) );
			$results = quadro::$wpdb->query( quadro::$wpdb->prepare($ativaSelecionada) );

			if ( $results ) return true;
			else return false;
		}

	}

	public function desativa_campanhas() {
		if ( is_null(quadro::$wpdb) )
	        quadro::inicializar();
	
		$desativaTodas = "UPDATE `".quadro::$wpdb->prefix."quadro_avisos` SET ativo = 0";
		$results = quadro::$wpdb->query( quadro::$wpdb->prepare($desativaTodas) );
		
		if ( $results ) return true;
		else return false;
	}

	function menu_mural() {
		//add_options_page('Quadro de avisos', 'Quadro de avisos', 10, 'quadro/gerenciar.php');
		  add_menu_page( 'Quadro de avisos', 'Quadro de avisos', 10, 'quadro/gerenciar.php', '', '', 10 );
	}

} // end class



$pathPlugin = substr(strrchr(dirname(__FILE__),DIRECTORY_SEPARATOR),1).DIRECTORY_SEPARATOR.basename(__FILE__);

add_action('admin_menu', array('quadro', 'menu_mural'));
register_activation_hook($pathPlugin, array('quadro', 'ativar'));      // activation
register_deactivation_hook($pathPlugin, array('quadro', 'desativar')); // deactivation
?>
