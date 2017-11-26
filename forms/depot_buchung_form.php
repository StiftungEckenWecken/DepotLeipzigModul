<?php

/**
 * Generates the booking editing form.
 */
function depot_booking_edit_form($form, &$form_state, $booking) {

  global $user;

 /* // 1. Validate URI-params
  $required_params = array(
    'rid' => array('int' => TRUE),
    'start' => array('int' => TRUE),
    'end' => array('int' => TRUE),
    'einheiten' => array('int' => TRUE)
  );*/

  $params = array();

  foreach (drupal_get_query_parameters() as $param => $val){
    $params[$param] = $val;
   /* if (isset($required_params[$param])){
      if (isset($required_params[$param]['int'])){ // TODO is_int
        unset($required_params[$param]);
        $params[$param] = $val;
      }
    }*/
  }

  $begin = (new DateTime())->setTimestamp($params['begin']);
  $end = (new DateTime())->setTimestamp($params['end']);
  $begin_bat = date_format($begin, 'Y-m-d H:i');
  $end_bat = date_format($end, 'Y-m-d H:i');

  /*if (!empty($required_params)){
    drupal_set_message(t('Fehlerhafte oder falsche Parameter. Bitte erneut probieren.'));
    drupal_access_denied();
  }*/

  // 2. Manually check again for availability of resources
  $avail_units = count(depot_get_available_units_by_rid($params['rid'], $begin_bat, $end_bat, true));

  if (empty($avail_units)){
    drupal_set_message(t('Keine Einheiten in diesem Zeitraum verfügbar'),'error');
    drupal_goto('ressourcen/'.$params['rid']);
  }

  $ressource = bat_type_load($params['rid']);

  if (empty($ressource)){
    drupal_set_message(t('Diese Ressource existiert nicht.'),'error');
    drupal_goto('ressourcen');
  }

  $ressource = get_object_vars($ressource);  
  $user_is_organisation = in_array(ROLE_ORGANISATION_NAME ,$user->roles);
  
  if (isset($ressource['field_gemeinwohl']['und'][0]['value']) && !$user_is_organisation){
    drupal_set_message(t('Dieses Angebot ist leider nur für gemeinnützig anerkannte Organisationen reservierbar.'),'error');
    drupal_goto('ressourcen');
  }

  $anbieter = user_load($ressource['uid']);
  $anbieter->is_organisation = in_array(ROLE_ORGANISATION_NAME ,$anbieter->roles);
  $allow_time_change = false;
  
  // Calculate price
  $res_preis = (isset($ressource['field_gemeinwohl']['und'][0]['value'])) ? $ressource['field_kosten_2']['und'][0]['value'] : $ressource['field_kosten']['und'][0]['value'];
  $res_kaution = (isset($ressource['field_kaution']['und'][0]['value'])) ? $ressource['field_kaution']['und'][0]['value'] : 0;
  $res_takt = $ressource['field_abrechnungstakt']['und'][0]['value'];
  $res_tax = (isset($ressource['field_mwst']['und'])) ? $ressource['field_mwst']['und'][0]['value'] : 0;
  $res_tax = '13';

  $preis = 0;
  $preis_tax = 0;
  $preis_kaution = 0;
  $preis_total = 0;

  if ($res_takt == 0){
    // granulartiy: daily
    $total_time = $begin->diff($end)->format('%a');
  } else {
    // hourly
    $total_time = $begin->diff($end)->format('%H:%I');   
  }

  if ($res_kaution > 0){
    $preis_kaution = $res_kaution * $params['einheiten'];
  }

  $preis = $res_preis * $total_time * $params['einheiten'];

  if ($res_tax > 0){
    $preis_tax = $preis * $res_tax / 100;
    $preis_total = $preis + $preis_tax;
  } else {
    $preis_total = $preis;
  }

  $preis_total += $preis_kaution;

  // 3. Add the field related form elements.
  $form_state['bat_booking'] = $booking;
  field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL);

  $res_card = '<div class="panel medium-5 column"><h4>'.$ressource['name'].'</h4><hr />';
  if ($anbieter->is_organisation){
    $res_card .= '<p>'.$anbieter->field_organisation_name['und'][0]['value'] .' ('. $anbieter->field_organisation_typ['und'][0]['value'] .')</p>';
  } else {
    $res_card .= '<p><strong>'.t('Anbieter').':</strong> '.$anbieter->field_anrede['und'][0]['value'].' '.$anbieter->field_vorname['und'][0]['value'].' '.$anbieter->field_nachname['und'][0]['value'].'</p>';
  }
  $res_card .= '</div>';
  $form['res_card'] = array(
    '#markup' => $res_card,
    '#weight' => '-99'
  );

  $price_card_tooltip = '
  <p><strong>'.t('Preis pro Einheit').':</strong> '. $res_preis . '€' . ($res_kaution > 0 ? ' | '.t('Kaution pro Einheit').':'. $res_kaution.'€' : '').'</p>
  ';

  $price_card = '
  <div class="panel callout medium-5 medium-offset-2 column" title="'.$price_card_tooltip.'">
    <h4>'. t('Preis').'</h4><hr />
     '. t('Netto') .': '. $preis .'€<br />
     '. ($preis_tax > 0 ? t('MwSt').': '. $preis_tax .'€<br />' : '') .'
     '. t('Brutto') .': '. $preis_total .'€<br />
     '. ($preis_kaution > 0 ? t('Kaution').':'.$preis_kaution.'€<br />' : '') .' 
    </div>';
 $form['price_card'] = array(
   '#markup' => $price_card,
   '#weight' => '-98'
 );

  $form['booking_start_date']['#attributes']['class'][] = 'medium-6 column';
  $form['booking_start_date']['#disabled'] = !$allow_time_change;
  $form['booking_start_date']['und'][0]['#title'] = t('Start Ausleihe');
  $form['booking_start_date']['und'][0]['#default_value']['value'] = $begin_bat;
  $form['booking_end_date']['#attributes']['class'][] = 'medium-6 column';
  $form['booking_end_date']['#disabled'] = !$allow_time_change;
  $form['booking_end_date']['und'][0]['#title'] = t('Ende Ausleihe');
  $form['booking_end_date']['und'][0]['#default_value']['value'] = $end_bat;
  
  //print_r($form['field_ausleiher']);
  $form['field_ausleiher']['#value'] = 'test (14)';

  // BAT Altlasten
  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => 99,
  );

  $access = user_has_role(ROLE_ADMINISTRATOR); // || user_access('bypass bat_booking entities access')

  // Type author information for administrators.
  $form['author'] = array(
    '#type' => 'fieldset',
    '#access' => $access,
    '#title' => t('Anleger Informationen'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'additional_settings',
    '#attributes' => array(
      'class' => array('type-form-author'),
    ),
    '#attached' => array(
      'js' => array(
        array(
          'type' => 'setting',
          'data' => array('anonymous' => variable_get('anonymous', t('Anonymous'))),
        ),
      ),
    ),
    '#weight' => 90,
  );

  $form['type'] = array(
    '#type' => 'value',
    '#value' => $booking->type,
  );

  $form['author']['author_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Eingestellt von'),
    '#maxlength' => 60,
    '#autocomplete_path' => 'user/autocomplete',
    '#default_value' => !empty($booking->author_name) ? $booking->author_name : '',
    '#weight' => -1,
    '#description' => t('Leave blank for %anonymous.', array('%anonymous' => variable_get('anonymous', t('Anonymous')))),
  );
  $form['author']['date'] = array(
    '#type' => 'textfield',
    '#title' => t('Eingestellt am'),
    '#maxlength' => 25,
    '#description' => t('Format: %time. The date format is YYYY-MM-DD and %timezone is the time zone offset from UTC. Leave blank to use the time of form submission.', array('%time' => !empty($booking->date) ? date_format(date_create($booking->date), 'Y-m-d H:i:s O') : format_date($booking->created, 'custom', 'Y-m-d H:i:s O'), '%timezone' => !empty($booking->date) ? date_format(date_create($booking->date), 'O') : format_date($booking->created, 'custom', 'O'))),
    '#default_value' => !empty($booking->date) ? $booking->date : '',
  );

  $form['options'] = array(
    '#type' => 'fieldset',
    '#access' => $access,
    '#title' => t('Publishing options'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'additional_settings',
    '#attributes' => array(
      'class' => array('booking-form-published'),
    ),
    '#weight' => 95,
  );
  $form['options']['status'] = array(
    '#type' => 'checkbox',
    '#title' => t('Published'),
    '#default_value' => $booking->status,
  );

  // Zusammenfassung Ressource

  // Kontakt Ressourcen-Inhaber

  // Zeitraum, Menge, Preis

  // Übergabe:

  $form['field_ausleiher_id']['#type'] = 'hidden';
  $form['field_ausleiher_id']['#value'] = $user->uid;


  $options = array();
  // $result[''] = '<'.t('Alle').'>';
  for ($i = 1; $i<=$avail_units; $i++){
    $options[$i] = $i;
  }

  $options_selected = ($params['einheiten'] > end($options)) ? end($options) : $params['einheiten'];

  //$form['field_einheiten']['#default_value'] = $params['einheiten'];
 //$form['field_einheiten']['#required'] = TRUE;
  $form['field_einheiten']['und']['#attributes']['class'][] = 'medium-4 column';
  $form['field_einheiten']['und']['#disabled'] = TRUE;
  $form['field_einheiten']['und']['#options'] = $options;
  $form['field_einheiten']['und']['#default_value'] = array($options_selected => $options_selected);
 
  $form['actions'] = array(
    '#type' => 'actions',
    '#tree' => FALSE,
  );
  // We add the form's #submit array to this button along with the actual submit
  // handler to preserve any submit handlers added by a form callback_wrapper.
  $submit = array();
  if (!empty($form['#submit'])) {
    $submit += $form['#submit'];
  }
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Buchung anfragen'),
    '#submit' => $submit + array('depot_booking_edit_form_submit'),
  );
  if (!empty($booking->label) && bat_booking_access('delete', $booking)) {
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Buchung entfernen'),
      '#submit' => $submit + array('depot_booking_form_submit_delete'),
      '#weight' => 45,
    );
  }

  // Depending on whether the form is in a popup or a normal page we need to change
  // the behavior of the cancel button.
  if (isset($form_state['ajax']) && $form_state['ajax'] == TRUE) {
    unset($form['actions']['cancel']);
  }
  else {
    $form['actions']['cancel'] = array(
      '#markup' => l(t('Cancel'), 'admin/bat/config/booking'),
      '#weight' => 50,
    );
  }

  $form['#validate'][] = 'depot_booking_edit_form_validate';

  return $form;
}

/**
 * Form API validation callback for the booking form.
 */
function depot_booking_edit_form_validate(&$form, &$form_state) {
  // Notify field widgets to validate their data.
  entity_form_field_validate('bat_booking', $form, $form_state);
}

/**
 * Form API submit callback for the booking form.
 */
function depot_booking_edit_form_submit(&$form, &$form_state) {

  $booking = entity_ui_controller('bat_booking')->entityFormSubmitBuildEntity($form, $form_state);

  $booking->created = !empty($booking->date) ? strtotime($booking->date) : REQUEST_TIME;
  $booking->changed = time();

  if (isset($booking->author_name)) {
    if ($account = user_load_by_name($booking->author_name)) {
      $booking->uid = $account->uid;
    }
    else {
      $booking->uid = 0;
    }
  }

  $booking->save();
  drupal_set_message(t('Ihre Reservierung wurde gespeichert und der Ressourceninhaber informiert. Dieser wird sich mit Ihnen in Verbindung setzen.'));

  $form_state['redirect'] = 'mein-depot/reservierungen';
  
}

/**
 * Form API submit callback for the delete button.
 */
function depot_booking_form_submit_delete(&$form, &$form_state) {
  if (isset($form_state['ajax'])) {
    bat_booking_delete($form_state['bat_booking']);
    drupal_set_message(t('The booking has been removed'));
    $form_state['booking_deleted'] = TRUE;
  }
  else {
    $destination = array();
    if (isset($_GET['destination'])) {
      $destination = drupal_get_destination();
      unset($_GET['destination']);
    }

    $form_state['redirect'] = array('admin/bat/config/booking/manage/' . $form_state['bat_booking']->booking_id . '/delete', array('query' => $destination));
  }
}
