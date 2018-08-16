$view = new view();
$view->name = 'ressourcen_detailseite';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'bat_types';
$view->human_name = 'Ressourcen Detailseite';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Ressource buchen';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'none';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['exposed_form']['options']['submit_button'] = 'Filtern';
$handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Sortieren';
$handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'Aufsteigend';
$handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'Absteigend';
$handler->display->display_options['exposed_form']['options']['autosubmit'] = TRUE;
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['pager']['options']['items_per_page'] = '20';
$handler->display->display_options['pager']['options']['tags']['first'] = '« erste Seite';
$handler->display->display_options['pager']['options']['tags']['previous'] = '‹ vorherige Seite';
$handler->display->display_options['pager']['options']['tags']['next'] = 'nächste Seite ›';
$handler->display->display_options['pager']['options']['tags']['last'] = 'letzte Seite »';
$handler->display->display_options['style_plugin'] = 'default';
$handler->display->display_options['row_plugin'] = 'fields';
/* Kopfbereich: Global: Unfiltered text */
$handler->display->display_options['header']['area_text_custom']['id'] = 'area_text_custom';
$handler->display->display_options['header']['area_text_custom']['table'] = 'views';
$handler->display->display_options['header']['area_text_custom']['field'] = 'area_text_custom';
$handler->display->display_options['header']['area_text_custom']['content'] = 'Buchen Sie jetzt [name] in [field_bezirk].';
$handler->display->display_options['header']['area_text_custom']['tokenize'] = TRUE;
/* Fußbereich: Global: Unfiltered text */
$handler->display->display_options['footer']['area_text_custom']['id'] = 'area_text_custom';
$handler->display->display_options['footer']['area_text_custom']['table'] = 'views';
$handler->display->display_options['footer']['area_text_custom']['field'] = 'area_text_custom';
$handler->display->display_options['footer']['area_text_custom']['content'] = '<a href="/buchungen/neu/[type_id]?destination=/buchungen/neu/[type_id]">Jetzt [name] buchen</a>';
$handler->display->display_options['footer']['area_text_custom']['tokenize'] = TRUE;
/* Relationship: Type revision: Benutzer */
$handler->display->display_options['relationships']['revision_uid']['id'] = 'revision_uid';
$handler->display->display_options['relationships']['revision_uid']['table'] = 'bat_types_revision';
$handler->display->display_options['relationships']['revision_uid']['field'] = 'revision_uid';
/* Feld: Type revision: Name */
$handler->display->display_options['fields']['name']['id'] = 'name';
$handler->display->display_options['fields']['name']['table'] = 'bat_types_revision';
$handler->display->display_options['fields']['name']['field'] = 'name';
$handler->display->display_options['fields']['name']['label'] = '';
$handler->display->display_options['fields']['name']['element_type'] = 'h1';
$handler->display->display_options['fields']['name']['element_label_colon'] = FALSE;
/* Feld: Typ: Bild I */
$handler->display->display_options['fields']['field_bild_i']['id'] = 'field_bild_i';
$handler->display->display_options['fields']['field_bild_i']['table'] = 'field_data_field_bild_i';
$handler->display->display_options['fields']['field_bild_i']['field'] = 'field_bild_i';
$handler->display->display_options['fields']['field_bild_i']['label'] = '';
$handler->display->display_options['fields']['field_bild_i']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['field_bild_i']['click_sort_column'] = 'fid';
$handler->display->display_options['fields']['field_bild_i']['settings'] = array(
  'image_style' => 'medium',
  'image_link' => 'content',
);
/* Feld: Typ: Bezirk */
$handler->display->display_options['fields']['field_bezirk']['id'] = 'field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['table'] = 'field_data_field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['field'] = 'field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['label'] = '';
$handler->display->display_options['fields']['field_bezirk']['element_label_colon'] = FALSE;
/* Feld: Typ: Preis ermäßigt */
$handler->display->display_options['fields']['field_kosten_2']['id'] = 'field_kosten_2';
$handler->display->display_options['fields']['field_kosten_2']['table'] = 'field_data_field_kosten_2';
$handler->display->display_options['fields']['field_kosten_2']['field'] = 'field_kosten_2';
$handler->display->display_options['fields']['field_kosten_2']['hide_empty'] = TRUE;
$handler->display->display_options['fields']['field_kosten_2']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Typ: Preis normal */
$handler->display->display_options['fields']['field_kosten']['id'] = 'field_kosten';
$handler->display->display_options['fields']['field_kosten']['table'] = 'field_data_field_kosten';
$handler->display->display_options['fields']['field_kosten']['field'] = 'field_kosten';
$handler->display->display_options['fields']['field_kosten']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Typ: Kategorie */
$handler->display->display_options['fields']['field_kategorie']['id'] = 'field_kategorie';
$handler->display->display_options['fields']['field_kategorie']['table'] = 'field_data_field_kategorie';
$handler->display->display_options['fields']['field_kategorie']['field'] = 'field_kategorie';
/* Feld: Type revision: Type ID */
$handler->display->display_options['fields']['type_id']['id'] = 'type_id';
$handler->display->display_options['fields']['type_id']['table'] = 'bat_types_revision';
$handler->display->display_options['fields']['type_id']['field'] = 'type_id';
$handler->display->display_options['fields']['type_id']['exclude'] = TRUE;
/* Feld: Benutzer: Name */
$handler->display->display_options['fields']['name_1']['id'] = 'name_1';
$handler->display->display_options['fields']['name_1']['table'] = 'users';
$handler->display->display_options['fields']['name_1']['field'] = 'name';
$handler->display->display_options['fields']['name_1']['relationship'] = 'revision_uid';
$handler->display->display_options['fields']['name_1']['label'] = 'Akteur';
/* Feld: Benutzer: E-Mail */
$handler->display->display_options['fields']['mail']['id'] = 'mail';
$handler->display->display_options['fields']['mail']['table'] = 'users';
$handler->display->display_options['fields']['mail']['field'] = 'mail';
$handler->display->display_options['fields']['mail']['relationship'] = 'revision_uid';
$handler->display->display_options['fields']['mail']['label'] = 'Akteur E-Mail';
/* Feld: Typ: Edit Link */
$handler->display->display_options['fields']['edit_type']['id'] = 'edit_type';
$handler->display->display_options['fields']['edit_type']['table'] = 'bat_types';
$handler->display->display_options['fields']['edit_type']['field'] = 'edit_type';
$handler->display->display_options['fields']['edit_type']['label'] = 'Ressource bearbeiten';
$handler->display->display_options['fields']['edit_type']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['edit_type']['hide_empty'] = TRUE;
/* Feld: Typ: Delete Link */
$handler->display->display_options['fields']['delete_type']['id'] = 'delete_type';
$handler->display->display_options['fields']['delete_type']['table'] = 'bat_types';
$handler->display->display_options['fields']['delete_type']['field'] = 'delete_type';
$handler->display->display_options['fields']['delete_type']['label'] = 'Ressource löschen?';
$handler->display->display_options['fields']['delete_type']['element_label_colon'] = FALSE;
/* Contextual filter: Typ: Typ ID */
$handler->display->display_options['arguments']['type_id']['id'] = 'type_id';
$handler->display->display_options['arguments']['type_id']['table'] = 'bat_types';
$handler->display->display_options['arguments']['type_id']['field'] = 'type_id';
$handler->display->display_options['arguments']['type_id']['default_action'] = 'not found';
$handler->display->display_options['arguments']['type_id']['exception']['title'] = 'Alle';
$handler->display->display_options['arguments']['type_id']['default_argument_type'] = 'node';
$handler->display->display_options['arguments']['type_id']['summary']['number_of_records'] = '0';
$handler->display->display_options['arguments']['type_id']['summary']['format'] = 'default_summary';
$handler->display->display_options['arguments']['type_id']['summary_options']['items_per_page'] = '25';

/* Display: Page */
$handler = $view->new_display('page', 'Page', 'page');
$handler->display->display_options['path'] = 'ressourcen/%';
$handler->display->display_options['menu']['title'] = 'Ressourcen Übersicht';
$handler->display->display_options['menu']['weight'] = '0';
$handler->display->display_options['menu']['name'] = 'main-menu';
$handler->display->display_options['menu']['context'] = 0;
$handler->display->display_options['menu']['context_only_inline'] = 0;
$translatables['ressourcen_detailseite'] = array(
  t('Master'),
  t('Ressource buchen'),
  t('more'),
  t('Filtern'),
  t('Reset'),
  t('Sortieren'),
  t('Aufsteigend'),
  t('Absteigend'),
  t('Items per page'),
  t('- All -'),
  t('Offset'),
  t('« erste Seite'),
  t('‹ vorherige Seite'),
  t('nächste Seite ›'),
  t('letzte Seite »'),
  t('Buchen Sie jetzt [name] in [field_bezirk].'),
  t('<a href="/buchungen/neu/[type_id]?destination=/buchungen/neu/[type_id]">Jetzt [name] buchen</a>'),
  t('Revision user'),
  t('Preis ermäßigt'),
  t('Preis normal'),
  t('Kategorie'),
  t('Type ID'),
  t('Akteur'),
  t('Akteur E-Mail'),
  t('Ressource bearbeiten'),
  t('Ressource löschen?'),
  t('Alle'),
  t('Page'),
);
