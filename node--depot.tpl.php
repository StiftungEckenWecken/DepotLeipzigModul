<?php 

function depot_preprocess_views_view_table(&$vars) {

  echo $vars['view']->name; exit();
  
  if ($vars['view']->name == 'neueste_ressourcen') {
    $vars['header'] = array();
  }
  
}