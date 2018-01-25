$view = new view();
$view->name = 'ressourcen_bersicht';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'bat_types';
$view->human_name = 'Ressourcen Übersicht';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Ressourcen teilen - Gemeinwohl stärken';
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
$handler->display->display_options['pager']['options']['items_per_page'] = '24';
$handler->display->display_options['pager']['options']['offset'] = '0';
$handler->display->display_options['pager']['options']['id'] = '0';
$handler->display->display_options['pager']['options']['quantity'] = '9';
$handler->display->display_options['pager']['options']['tags']['first'] = '« erste Seite';
$handler->display->display_options['pager']['options']['tags']['previous'] = '‹ vorherige Seite';
$handler->display->display_options['pager']['options']['tags']['next'] = 'nächste Seite ›';
$handler->display->display_options['pager']['options']['tags']['last'] = 'letzte Seite »';
$handler->display->display_options['style_plugin'] = 'default';
$handler->display->display_options['style_options']['row_class'] = 'res-elem medium-4 small-12 column';
$handler->display->display_options['style_options']['default_row_class'] = FALSE;
$handler->display->display_options['row_plugin'] = 'fields';
/* Kopfbereich: Global: Unfiltered text */
$handler->display->display_options['header']['area_text_custom']['id'] = 'area_text_custom';
$handler->display->display_options['header']['area_text_custom']['table'] = 'views';
$handler->display->display_options['header']['area_text_custom']['field'] = 'area_text_custom';
$handler->display->display_options['header']['area_text_custom']['label'] = 'Intro text';
$handler->display->display_options['header']['area_text_custom']['empty'] = TRUE;
$handler->display->display_options['header']['area_text_custom']['content'] = '<p>Das <span class="rs">Depot Leipzig</span> ist Deine Plattform um das Verwalten und Teilen von Ressourcen noch einfacher und attraktiver zu machen. Schau\' rein und mach mit!</p>

<p class="text-center"><strong>So geht\'s: 1. Ressource aussuchen - 2. Verfügbarkeit prüfen - 3. Reservierung anfragen</strong></p> 
<ul class="button-group even-2 stack-for-small ressource-page-button-group" id="button-group-logged-out">
  <li><a href="/user/login?destination=/ressourcen/neu" class="button small"><i class="fi fi-unlock"></i> Einloggen & Ressource anlegen</a></li>
  <li><a href="/faq" class="button small"><i class="fi fi-megaphone"></i> Mehr erfahren</a></li>
</ul>

<ul class="button-group even-2 stack-for-small ressource-page-button-group" id="button-group-logged-in">
  <li><a href="/ressourcen/neu" class="button small"><i class="fi fi-pencil"></i> Ressource anlegen</a></li>
  <li><a href="/mein-depot" class="button small ci"><i class="fi fi-star"></i> Mein Depot</a></li>
</ul>';
/* Fußbereich: Global: View area */
$handler->display->display_options['footer']['view']['id'] = 'view';
$handler->display->display_options['footer']['view']['table'] = 'views';
$handler->display->display_options['footer']['view']['field'] = 'view';
/* No results behavior: Global: Unfiltered text */
$handler->display->display_options['empty']['area_text_custom']['id'] = 'area_text_custom';
$handler->display->display_options['empty']['area_text_custom']['table'] = 'views';
$handler->display->display_options['empty']['area_text_custom']['field'] = 'area_text_custom';
$handler->display->display_options['empty']['area_text_custom']['label'] = 'Keine Ressourcen';
$handler->display->display_options['empty']['area_text_custom']['empty'] = TRUE;
$handler->display->display_options['empty']['area_text_custom']['content'] = '<p class="text-center"><span class="warning label">Nix da...</span> ...wir konnten keine Ressourcen mit diesen Filterangaben finden :(</p>';
/* Feld: Typ: Typ ID */
$handler->display->display_options['fields']['type_id']['id'] = 'type_id';
$handler->display->display_options['fields']['type_id']['table'] = 'bat_types';
$handler->display->display_options['fields']['type_id']['field'] = 'type_id';
$handler->display->display_options['fields']['type_id']['label'] = '';
$handler->display->display_options['fields']['type_id']['exclude'] = TRUE;
$handler->display->display_options['fields']['type_id']['element_label_colon'] = FALSE;
/* Feld: Typ: Abrechnungstakt */
$handler->display->display_options['fields']['field_abrechnungstakt']['id'] = 'field_abrechnungstakt';
$handler->display->display_options['fields']['field_abrechnungstakt']['table'] = 'field_data_field_abrechnungstakt';
$handler->display->display_options['fields']['field_abrechnungstakt']['field'] = 'field_abrechnungstakt';
$handler->display->display_options['fields']['field_abrechnungstakt']['label'] = '';
$handler->display->display_options['fields']['field_abrechnungstakt']['exclude'] = TRUE;
$handler->display->display_options['fields']['field_abrechnungstakt']['element_label_colon'] = FALSE;
/* Feld: Typ: Bild I */
$handler->display->display_options['fields']['field_bild_i']['id'] = 'field_bild_i';
$handler->display->display_options['fields']['field_bild_i']['table'] = 'field_data_field_bild_i';
$handler->display->display_options['fields']['field_bild_i']['field'] = 'field_bild_i';
$handler->display->display_options['fields']['field_bild_i']['label'] = '';
$handler->display->display_options['fields']['field_bild_i']['alter']['make_link'] = TRUE;
$handler->display->display_options['fields']['field_bild_i']['alter']['path'] = 'ressourcen/[type_id]';
$handler->display->display_options['fields']['field_bild_i']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['field_bild_i']['click_sort_column'] = 'fid';
$handler->display->display_options['fields']['field_bild_i']['settings'] = array(
  'image_style' => 'medium',
  'image_link' => '',
);
/* Feld: Typ: Kategorie */
$handler->display->display_options['fields']['field_kategorie']['id'] = 'field_kategorie';
$handler->display->display_options['fields']['field_kategorie']['table'] = 'field_data_field_kategorie';
$handler->display->display_options['fields']['field_kategorie']['field'] = 'field_kategorie';
$handler->display->display_options['fields']['field_kategorie']['label'] = '';
$handler->display->display_options['fields']['field_kategorie']['alter']['alter_text'] = TRUE;
$handler->display->display_options['fields']['field_kategorie']['alter']['text'] = '[field_kategorie]';
$handler->display->display_options['fields']['field_kategorie']['alter']['make_link'] = TRUE;
$handler->display->display_options['fields']['field_kategorie']['alter']['path'] = 'ressourcen/?kategorie_id=[field_kategorie-state_id] ';
$handler->display->display_options['fields']['field_kategorie']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['field_kategorie']['hide_empty'] = TRUE;
/* Feld: Type revision: Name */
$handler->display->display_options['fields']['name']['id'] = 'name';
$handler->display->display_options['fields']['name']['table'] = 'bat_types_revision';
$handler->display->display_options['fields']['name']['field'] = 'name';
$handler->display->display_options['fields']['name']['label'] = '';
$handler->display->display_options['fields']['name']['element_type'] = 'h4';
$handler->display->display_options['fields']['name']['element_label_colon'] = FALSE;
/* Feld: Typ: Postleitzahl */
$handler->display->display_options['fields']['field_adresse_postleitzahl']['id'] = 'field_adresse_postleitzahl';
$handler->display->display_options['fields']['field_adresse_postleitzahl']['table'] = 'field_data_field_adresse_postleitzahl';
$handler->display->display_options['fields']['field_adresse_postleitzahl']['field'] = 'field_adresse_postleitzahl';
$handler->display->display_options['fields']['field_adresse_postleitzahl']['label'] = '';
$handler->display->display_options['fields']['field_adresse_postleitzahl']['exclude'] = TRUE;
$handler->display->display_options['fields']['field_adresse_postleitzahl']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['field_adresse_postleitzahl']['type'] = 'number_integer';
$handler->display->display_options['fields']['field_adresse_postleitzahl']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Typ: Bezirk */
$handler->display->display_options['fields']['field_bezirk']['id'] = 'field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['table'] = 'field_data_field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['field'] = 'field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['label'] = '';
$handler->display->display_options['fields']['field_bezirk']['alter']['alter_text'] = TRUE;
$handler->display->display_options['fields']['field_bezirk']['alter']['text'] = '<i class="fi fi-marker"></i>[field_adresse_postleitzahl] ([field_bezirk])';
$handler->display->display_options['fields']['field_bezirk']['element_label_colon'] = FALSE;
/* Feld: Typ: Anzahl Einheiten */
$handler->display->display_options['fields']['field_anzahl_einheiten']['id'] = 'field_anzahl_einheiten';
$handler->display->display_options['fields']['field_anzahl_einheiten']['table'] = 'field_data_field_anzahl_einheiten';
$handler->display->display_options['fields']['field_anzahl_einheiten']['field'] = 'field_anzahl_einheiten';
$handler->display->display_options['fields']['field_anzahl_einheiten']['label'] = '';
$handler->display->display_options['fields']['field_anzahl_einheiten']['alter']['alter_text'] = TRUE;
$handler->display->display_options['fields']['field_anzahl_einheiten']['alter']['text'] = '<i class="fi fi-thumbnails"></i>Einheiten: [field_anzahl_einheiten-value]';
$handler->display->display_options['fields']['field_anzahl_einheiten']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['field_anzahl_einheiten']['type'] = 'number_unformatted';
$handler->display->display_options['fields']['field_anzahl_einheiten']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Typ: Preis */
$handler->display->display_options['fields']['field_kosten']['id'] = 'field_kosten';
$handler->display->display_options['fields']['field_kosten']['table'] = 'field_data_field_kosten';
$handler->display->display_options['fields']['field_kosten']['field'] = 'field_kosten';
$handler->display->display_options['fields']['field_kosten']['label'] = '';
$handler->display->display_options['fields']['field_kosten']['alter']['alter_text'] = TRUE;
$handler->display->display_options['fields']['field_kosten']['alter']['text'] = '[field_kosten-value] € / [field_abrechnungstakt]';
$handler->display->display_options['fields']['field_kosten']['element_label_colon'] = FALSE;
$handler->display->display_options['fields']['field_kosten']['type'] = 'number_unformatted';
$handler->display->display_options['fields']['field_kosten']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Typ: Preis ermäßigt */
$handler->display->display_options['fields']['field_kosten_2']['id'] = 'field_kosten_2';
$handler->display->display_options['fields']['field_kosten_2']['table'] = 'field_data_field_kosten_2';
$handler->display->display_options['fields']['field_kosten_2']['field'] = 'field_kosten_2';
$handler->display->display_options['fields']['field_kosten_2']['label'] = 'Ermäßigt';
$handler->display->display_options['fields']['field_kosten_2']['hide_empty'] = TRUE;
$handler->display->display_options['fields']['field_kosten_2']['type'] = 'number_unformatted';
$handler->display->display_options['fields']['field_kosten_2']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Global: Custom text */
$handler->display->display_options['fields']['nothing']['id'] = 'nothing';
$handler->display->display_options['fields']['nothing']['table'] = 'views';
$handler->display->display_options['fields']['nothing']['field'] = 'nothing';
$handler->display->display_options['fields']['nothing']['label'] = '';
$handler->display->display_options['fields']['nothing']['alter']['text'] = '<br />
<a class="button secondary small expand" href="ressourcen/[type_id]">&raquo; [name] reservieren!</a>';
$handler->display->display_options['fields']['nothing']['element_label_colon'] = FALSE;
/* Sort criterion: Typ: Created */
$handler->display->display_options['sorts']['created']['id'] = 'created';
$handler->display->display_options['sorts']['created']['table'] = 'bat_types';
$handler->display->display_options['sorts']['created']['field'] = 'created';
$handler->display->display_options['sorts']['created']['order'] = 'DESC';
/* Filter criterion: Typ: Type */
$handler->display->display_options['filters']['type']['id'] = 'type';
$handler->display->display_options['filters']['type']['table'] = 'bat_types';
$handler->display->display_options['filters']['type']['field'] = 'type';
$handler->display->display_options['filters']['type']['value'] = array(
  'depot' => 'depot',
);
/* Filter criterion: Typ: Bezirk (field_bezirk) */
$handler->display->display_options['filters']['field_bezirk_state_id']['id'] = 'field_bezirk_state_id';
$handler->display->display_options['filters']['field_bezirk_state_id']['table'] = 'field_data_field_bezirk';
$handler->display->display_options['filters']['field_bezirk_state_id']['field'] = 'field_bezirk_state_id';
$handler->display->display_options['filters']['field_bezirk_state_id']['exposed'] = TRUE;
$handler->display->display_options['filters']['field_bezirk_state_id']['expose']['operator_id'] = 'field_bezirk_state_id_op';
$handler->display->display_options['filters']['field_bezirk_state_id']['expose']['label'] = 'Bezirk';
$handler->display->display_options['filters']['field_bezirk_state_id']['expose']['operator'] = 'field_bezirk_state_id_op';
$handler->display->display_options['filters']['field_bezirk_state_id']['expose']['identifier'] = 'bezirk_id';
$handler->display->display_options['filters']['field_bezirk_state_id']['expose']['remember_roles'] = array(
  2 => '2',
  1 => 0,
);
$handler->display->display_options['filters']['field_bezirk_state_id']['group_info']['label'] = 'Bezirk (field_bezirk)';
$handler->display->display_options['filters']['field_bezirk_state_id']['group_info']['identifier'] = 'field_bezirk_state_id';
$handler->display->display_options['filters']['field_bezirk_state_id']['group_info']['group_items'] = array(
  1 => array(
    'title' => '',
    'operator' => '=',
    'value' => array(
      'value' => '',
      'min' => '',
      'max' => '',
    ),
  ),
  2 => array(
    'title' => '',
    'operator' => '=',
    'value' => array(
      'value' => '',
      'min' => '',
      'max' => '',
    ),
  ),
  3 => array(
    'title' => '',
    'operator' => '=',
    'value' => array(
      'value' => '',
      'min' => '',
      'max' => '',
    ),
  ),
);
/* Filter criterion: Typ: Kategorie (field_kategorie) */
$handler->display->display_options['filters']['field_kategorie_state_id']['id'] = 'field_kategorie_state_id';
$handler->display->display_options['filters']['field_kategorie_state_id']['table'] = 'field_data_field_kategorie';
$handler->display->display_options['filters']['field_kategorie_state_id']['field'] = 'field_kategorie_state_id';
$handler->display->display_options['filters']['field_kategorie_state_id']['exposed'] = TRUE;
$handler->display->display_options['filters']['field_kategorie_state_id']['expose']['operator_id'] = 'field_kategorie_state_id_op';
$handler->display->display_options['filters']['field_kategorie_state_id']['expose']['label'] = 'Kategorie';
$handler->display->display_options['filters']['field_kategorie_state_id']['expose']['operator'] = 'field_kategorie_state_id_op';
$handler->display->display_options['filters']['field_kategorie_state_id']['expose']['identifier'] = 'kategorie_id';
$handler->display->display_options['filters']['field_kategorie_state_id']['expose']['remember_roles'] = array(
  2 => '2',
  1 => 0,
);
/* Filter criterion: Typ: Beschriftung */
$handler->display->display_options['filters']['name']['id'] = 'name';
$handler->display->display_options['filters']['name']['table'] = 'bat_types';
$handler->display->display_options['filters']['name']['field'] = 'name';
$handler->display->display_options['filters']['name']['operator'] = 'contains';
$handler->display->display_options['filters']['name']['exposed'] = TRUE;
$handler->display->display_options['filters']['name']['expose']['operator_id'] = 'name_op';
$handler->display->display_options['filters']['name']['expose']['label'] = 'Stichwort';
$handler->display->display_options['filters']['name']['expose']['operator'] = 'name_op';
$handler->display->display_options['filters']['name']['expose']['identifier'] = 'name';
$handler->display->display_options['filters']['name']['expose']['remember_roles'] = array(
  2 => '2',
  1 => '1',
  3 => 0,
  4 => 0,
);
/* Filter criterion: Typ: Aktiviert (field_aktiviert) */
$handler->display->display_options['filters']['field_aktiviert_value']['id'] = 'field_aktiviert_value';
$handler->display->display_options['filters']['field_aktiviert_value']['table'] = 'field_data_field_aktiviert';
$handler->display->display_options['filters']['field_aktiviert_value']['field'] = 'field_aktiviert_value';
$handler->display->display_options['filters']['field_aktiviert_value']['value'] = array(
  1 => '1',
);

/* Display: Page */
$handler = $view->new_display('page', 'Page', 'page');
$handler->display->display_options['path'] = 'ressourcen';
$handler->display->display_options['menu']['type'] = 'normal';
$handler->display->display_options['menu']['title'] = 'Ressourcen';
$handler->display->display_options['menu']['weight'] = '0';
$handler->display->display_options['menu']['name'] = 'main-menu';
$handler->display->display_options['menu']['context'] = 0;
$handler->display->display_options['menu']['context_only_inline'] = 0;
$translatables['ressourcen_bersicht'] = array(
  t('Master'),
  t('Ressourcen teilen - Gemeinwohl stärken'),
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
  t('Intro text'),
  t('<p>Das <span class="rs">Depot Leipzig</span> ist Deine Plattform um das Verwalten und Teilen von Ressourcen noch einfacher und attraktiver zu machen. Schau\' rein und mach mit!</p>

<p class="text-center"><strong>So geht\'s: 1. Ressource aussuchen - 2. Verfügbarkeit prüfen - 3. Reservierung anfragen</strong></p> 
<ul class="button-group even-2 stack-for-small ressource-page-button-group" id="button-group-logged-out">
  <li><a href="/user/login?destination=/ressourcen/neu" class="button small"><i class="fi fi-unlock"></i> Einloggen & Ressource anlegen</a></li>
  <li><a href="/faq" class="button small"><i class="fi fi-megaphone"></i> Mehr erfahren</a></li>
</ul>

<ul class="button-group even-2 stack-for-small ressource-page-button-group" id="button-group-logged-in">
  <li><a href="/ressourcen/neu" class="button small"><i class="fi fi-pencil"></i> Ressource anlegen</a></li>
  <li><a href="/mein-depot" class="button small ci"><i class="fi fi-star"></i> Mein Depot</a></li>
</ul>'),
  t('Keine Ressourcen'),
  t('<p class="text-center"><span class="warning label">Nix da...</span> ...wir konnten keine Ressourcen mit diesen Filterangaben finden :(</p>'),
  t('.'),
  t(','),
  t('[field_kategorie]'),
  t('<i class="fi fi-marker"></i>[field_adresse_postleitzahl] ([field_bezirk])'),
  t('<i class="fi fi-thumbnails"></i>Einheiten: [field_anzahl_einheiten-value]'),
  t('[field_kosten-value] € / [field_abrechnungstakt]'),
  t('Ermäßigt'),
  t('<br />
<a class="button secondary small expand" href="ressourcen/[type_id]">&raquo; [name] reservieren!</a>'),
  t('Bezirk'),
  t('Bezirk (field_bezirk)'),
  t('Kategorie'),
  t('Stichwort'),
  t('Page'),
);
