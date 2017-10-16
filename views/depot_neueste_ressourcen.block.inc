$view = new view();
$view->name = 'neueste_ressourcen';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'bat_types';
$view->human_name = 'Neueste Ressourcen';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Neueste Ressourcen';
$handler->display->display_options['use_more'] = TRUE;
$handler->display->display_options['use_more_always'] = TRUE;
$handler->display->display_options['use_more_text'] = 'Alle Ressourcen';
$handler->display->display_options['link_display'] = 'custom_url';
$handler->display->display_options['access']['type'] = 'none';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Zurücksetzen';
$handler->display->display_options['pager']['type'] = 'some';
$handler->display->display_options['pager']['options']['items_per_page'] = '6';
$handler->display->display_options['pager']['options']['offset'] = '0';
$handler->display->display_options['style_plugin'] = 'table';
$handler->display->display_options['style_options']['columns'] = array(
  'type_id' => 'type_id',
  'calendars' => 'calendars',
  'field_kosten' => 'field_kosten',
  'status' => 'status',
  'field_verfueg' => 'field_verfueg',
);
$handler->display->display_options['style_options']['default'] = '-1';
$handler->display->display_options['style_options']['info'] = array(
  'type_id' => array(
    'sortable' => 0,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'calendars' => array(
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'field_kosten' => array(
    'sortable' => 0,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'status' => array(
    'sortable' => 0,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'field_verfueg' => array(
    'sortable' => 0,
    'default_sort_order' => 'asc',
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
);
/* Feld: Typ: Typ ID */
$handler->display->display_options['fields']['type_id']['id'] = 'type_id';
$handler->display->display_options['fields']['type_id']['table'] = 'bat_types';
$handler->display->display_options['fields']['type_id']['field'] = 'type_id';
$handler->display->display_options['fields']['type_id']['label'] = '';
$handler->display->display_options['fields']['type_id']['exclude'] = TRUE;
$handler->display->display_options['fields']['type_id']['element_label_colon'] = FALSE;
/* Feld: Typ: Anzahl Einheiten */
$handler->display->display_options['fields']['field_anzahl_einheiten']['id'] = 'field_anzahl_einheiten';
$handler->display->display_options['fields']['field_anzahl_einheiten']['table'] = 'field_data_field_anzahl_einheiten';
$handler->display->display_options['fields']['field_anzahl_einheiten']['field'] = 'field_anzahl_einheiten';
$handler->display->display_options['fields']['field_anzahl_einheiten']['exclude'] = TRUE;
$handler->display->display_options['fields']['field_anzahl_einheiten']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Type revision: Name */
$handler->display->display_options['fields']['name']['id'] = 'name';
$handler->display->display_options['fields']['name']['table'] = 'bat_types_revision';
$handler->display->display_options['fields']['name']['field'] = 'name';
$handler->display->display_options['fields']['name']['alter']['alter_text'] = TRUE;
$handler->display->display_options['fields']['name']['alter']['text'] = '[name] ([field_anzahl_einheiten])';
/* Feld: Typ: Preis normal */
$handler->display->display_options['fields']['field_kosten']['id'] = 'field_kosten';
$handler->display->display_options['fields']['field_kosten']['table'] = 'field_data_field_kosten';
$handler->display->display_options['fields']['field_kosten']['field'] = 'field_kosten';
$handler->display->display_options['fields']['field_kosten']['label'] = 'Preis';
$handler->display->display_options['fields']['field_kosten']['settings'] = array(
  'thousand_separator' => '',
  'prefix_suffix' => 1,
);
/* Feld: Typ: Bezirk */
$handler->display->display_options['fields']['field_bezirk']['id'] = 'field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['table'] = 'field_data_field_bezirk';
$handler->display->display_options['fields']['field_bezirk']['field'] = 'field_bezirk';
/* Feld: Global: Custom text */
$handler->display->display_options['fields']['nothing']['id'] = 'nothing';
$handler->display->display_options['fields']['nothing']['table'] = 'views';
$handler->display->display_options['fields']['nothing']['field'] = 'nothing';
$handler->display->display_options['fields']['nothing']['label'] = 'Öffnen';
$handler->display->display_options['fields']['nothing']['alter']['text'] = '[name] öffnen';
$handler->display->display_options['fields']['nothing']['alter']['make_link'] = TRUE;
$handler->display->display_options['fields']['nothing']['alter']['path'] = 'ressourcen/[type_id]';
/* Sort criterion: Typ: Created */
$handler->display->display_options['sorts']['created']['id'] = 'created';
$handler->display->display_options['sorts']['created']['table'] = 'bat_types';
$handler->display->display_options['sorts']['created']['field'] = 'created';
/* Filter criterion: Typ: Type */
$handler->display->display_options['filters']['type']['id'] = 'type';
$handler->display->display_options['filters']['type']['table'] = 'bat_types';
$handler->display->display_options['filters']['type']['field'] = 'type';
$handler->display->display_options['filters']['type']['value'] = array(
  'depot' => 'depot',
);

/* Display: Block */
$handler = $view->new_display('block', 'Block', 'block');
$handler->display->display_options['link_url'] = 'ressourcen';
$translatables['neueste_ressourcen'] = array(
  t('Master'),
  t('Neueste Ressourcen'),
  t('Alle Ressourcen'),
  t('Apply'),
  t('Zurücksetzen'),
  t('Sort by'),
  t('Asc'),
  t('Desc'),
  t('.'),
  t(','),
  t('Anzahl Einheiten'),
  t('Name'),
  t('[name] ([field_anzahl_einheiten])'),
  t('Preis'),
  t('Bezirk'),
  t('Öffnen'),
  t('[name] öffnen'),
  t('Block'),
  t('more'),
);
