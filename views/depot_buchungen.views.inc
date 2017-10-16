$view = new view();
$view->name = 'buchungen';
$view->description = '';
$view->tag = 'default';
$view->base_table = 'bat_bookings';
$view->human_name = 'Buchungen';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Buchungen (ALLE)';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'none';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Zurücksetzen';
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['pager']['options']['items_per_page'] = '20';
$handler->display->display_options['pager']['options']['tags']['first'] = '« erste Seite';
$handler->display->display_options['pager']['options']['tags']['previous'] = '‹ vorherige Seite';
$handler->display->display_options['pager']['options']['tags']['next'] = 'nächste Seite ›';
$handler->display->display_options['pager']['options']['tags']['last'] = 'letzte Seite »';
$handler->display->display_options['style_plugin'] = 'table_megarows';
$handler->display->display_options['row_plugin'] = 'fields';
/* Kopfbereich: Global: Text area */
$handler->display->display_options['header']['area']['id'] = 'area';
$handler->display->display_options['header']['area']['table'] = 'views';
$handler->display->display_options['header']['area']['field'] = 'area';
$handler->display->display_options['header']['area']['content'] = 'Diese Seite dient der Administration von Buchungen. Sie wird später nur dem super-Admin zugänglich sein bzw. nur die dem Ausleiher/Entleiher zugeteilten Buchungen zeigen.';
$handler->display->display_options['header']['area']['format'] = 'plain_text';
/* Feld: BAT Booking: Bat booking ID */
$handler->display->display_options['fields']['booking_id']['id'] = 'booking_id';
$handler->display->display_options['fields']['booking_id']['table'] = 'bat_bookings';
$handler->display->display_options['fields']['booking_id']['field'] = 'booking_id';
$handler->display->display_options['fields']['booking_id']['label'] = 'Buchungs ID';
/* Feld: BAT Booking: Ausleiher */
$handler->display->display_options['fields']['field_ausleiher']['id'] = 'field_ausleiher';
$handler->display->display_options['fields']['field_ausleiher']['table'] = 'field_data_field_ausleiher';
$handler->display->display_options['fields']['field_ausleiher']['field'] = 'field_ausleiher';
$handler->display->display_options['fields']['field_ausleiher']['settings'] = array(
  'bypass_access' => 0,
  'link' => 0,
);
/* Feld: BAT Booking: Start Date */
$handler->display->display_options['fields']['booking_start_date']['id'] = 'booking_start_date';
$handler->display->display_options['fields']['booking_start_date']['table'] = 'field_data_booking_start_date';
$handler->display->display_options['fields']['booking_start_date']['field'] = 'booking_start_date';
$handler->display->display_options['fields']['booking_start_date']['label'] = 'Start';
$handler->display->display_options['fields']['booking_start_date']['settings'] = array(
  'format_type' => 'long',
  'custom_date_format' => '',
  'fromto' => 'both',
  'multiple_number' => '',
  'multiple_from' => '',
  'multiple_to' => '',
  'show_remaining_days' => 0,
);
/* Feld: BAT Booking: End Date */
$handler->display->display_options['fields']['booking_end_date']['id'] = 'booking_end_date';
$handler->display->display_options['fields']['booking_end_date']['table'] = 'field_data_booking_end_date';
$handler->display->display_options['fields']['booking_end_date']['field'] = 'booking_end_date';
$handler->display->display_options['fields']['booking_end_date']['label'] = 'Ende';
$handler->display->display_options['fields']['booking_end_date']['settings'] = array(
  'format_type' => 'long',
  'custom_date_format' => '',
  'fromto' => 'both',
  'multiple_number' => '',
  'multiple_from' => '',
  'multiple_to' => '',
  'show_remaining_days' => 0,
);
/* Feld: BAT Booking: Nights */
$handler->display->display_options['fields']['nights']['id'] = 'nights';
$handler->display->display_options['fields']['nights']['table'] = 'bat_bookings';
$handler->display->display_options['fields']['nights']['field'] = 'nights';
$handler->display->display_options['fields']['nights']['label'] = 'Tage';
/* Feld: BAT Booking: Edit Link */
$handler->display->display_options['fields']['edit_unit']['id'] = 'edit_unit';
$handler->display->display_options['fields']['edit_unit']['table'] = 'bat_bookings';
$handler->display->display_options['fields']['edit_unit']['field'] = 'edit_unit';
$handler->display->display_options['fields']['edit_unit']['label'] = 'Bearbeiten?';
/* Feld: BAT Booking: Delete Link */
$handler->display->display_options['fields']['delete_unit']['id'] = 'delete_unit';
$handler->display->display_options['fields']['delete_unit']['table'] = 'bat_bookings';
$handler->display->display_options['fields']['delete_unit']['field'] = 'delete_unit';
$handler->display->display_options['fields']['delete_unit']['label'] = 'Löschen?';
$handler->display->display_options['fields']['delete_unit']['element_label_colon'] = FALSE;
/* Filter criterion: BAT Booking: Type */
$handler->display->display_options['filters']['type']['id'] = 'type';
$handler->display->display_options['filters']['type']['table'] = 'bat_bookings';
$handler->display->display_options['filters']['type']['field'] = 'type';
$handler->display->display_options['filters']['type']['value'] = array(
  'standard' => 'standard',
);

/* Display: Page */
$handler = $view->new_display('page', 'Page', 'page');
$handler->display->display_options['path'] = 'buchungen';
$handler->display->display_options['menu']['type'] = 'normal';
$handler->display->display_options['menu']['title'] = 'Buchungen (Administration)';
$handler->display->display_options['menu']['weight'] = '0';
$handler->display->display_options['menu']['name'] = 'main-menu';
$handler->display->display_options['menu']['context'] = 0;
$handler->display->display_options['menu']['context_only_inline'] = 0;
$translatables['buchungen'] = array(
  t('Master'),
  t('Buchungen (ALLE)'),
  t('more'),
  t('Apply'),
  t('Zurücksetzen'),
  t('Sort by'),
  t('Asc'),
  t('Desc'),
  t('Items per page'),
  t('- All -'),
  t('Offset'),
  t('« erste Seite'),
  t('‹ vorherige Seite'),
  t('nächste Seite ›'),
  t('letzte Seite »'),
  t('Diese Seite dient der Administration von Buchungen. Sie wird später nur dem super-Admin zugänglich sein bzw. nur die dem Ausleiher/Entleiher zugeteilten Buchungen zeigen.'),
  t('Buchungs ID'),
  t('.'),
  t(','),
  t('Ausleiher'),
  t('Start'),
  t('Ende'),
  t('Tage'),
  t('Bearbeiten?'),
  t('Löschen?'),
  t('Page'),
);
