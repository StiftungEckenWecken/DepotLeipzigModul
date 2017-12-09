<?php

function machine_to_human_price($prices){

  if (!is_array($prices)){
    $prices = array($prices);
  }

  foreach ($prices as $key => $price){
    if (strpos($price,'.') != FALSE){
      if (strlen(substr($price,-1,2)) == 1){
        $prices[$key] = str_replace('.',',',$price).'0';
      }
    }
  }

  return $prices;

}

function __depot_booking_edit_form_set_params(&$params){

  foreach (drupal_get_query_parameters() as $param => $val){
    $params[$param] = $val;
  }

  if (!isset($params['rid']) || !isset($params['begin']) || !isset($params['end']) || !isset($params['einheiten'])){
    drupal_set_message(t('Unvollständige Parameter. Bitte informieren Sie den Administrator.'),'error');
    depot_form_goto('ressourcen/'.$params['rid']);
  }

}
/**
 * Generates the booking editing form.
 */
function depot_booking_edit_form($form, &$form_state, $booking) {

  global $user;
  $user = user_load($user->uid);
  $edit_mode = (!empty($booking->label));
  $params = array();
  $begin = null;
  $end = null;

  if ($edit_mode){

    $params = array(
      'rid' => $booking->field_ressource_id['und'][0]['value'],
      'begin' => $booking->booking_start_date['und'][0]['value'],
      'end' => $booking->booking_end_date['und'][0]['value'],
      'einheiten' => $booking->field_einheiten['und'][0]['value']
    );

    $begin = new DateTime($params['begin']);
    $end = new DateTime($params['end']);
    
    $preis = unserialize($booking->field_preis_meta['und'][0]['value']);
    
  } else {

    __depot_booking_edit_form_set_params($params);
    $begin = (new DateTime())->setTimestamp($params['begin']);
    $end = (new DateTime())->setTimestamp($params['end']);

  }

  // http://drupal.localhost/reservierungen/neu?rid=3&einheiten=2&begin=1518264000&end=1518436800
  $begin_bat = date_format($begin, 'Y-m-d H:i');
  $end_bat = date_format($end, 'Y-m-d H:i');

  /*if (!empty($required_params)){
    drupal_set_message(t('Fehlerhafte oder falsche Parameter. Bitte erneut probieren.'));
    drupal_access_denied();
  }*/

  // 2. Manually check again for availability of resources

  $begin_bat = date_format($begin, 'Y-m-d H:i');
  $end_bat = date_format($end, 'Y-m-d H:i');

  $avail_units = count(depot_get_available_units_by_rid($params['rid'], $begin_bat, $end_bat, true));  

  $ressource = bat_type_load($params['rid']);
  $ressource = get_object_vars($ressource);
  $user_is_organisation = in_array(ROLE_ORGANISATION_NAME ,$user->roles);
  $anbieter = user_load($ressource['revision_uid']);
  $user_is_anbieter = ($anbieter->uid === $user->uid);  
  $anbieter->is_organisation = in_array(ROLE_ORGANISATION_NAME ,$anbieter->roles);
  $allow_change = (user_has_role(ROLE_ADMINISTRATOR) || $edit_mode);  

  if ($ressource['field_abrechnungstakt']['und'][0]['value'] == 0){
    // granulartiy: daily
    $total_time = $begin->diff($end)->format('%a');
    $total_time_readable = $total_time . ' ' . ($total_time == 1 ? t('Tag') : t('Tage'));
  } else {
    // hourly
    $total_time = $begin->diff($end)->format('%H:%I');   
  }

  if (!$edit_mode){

    if (empty($avail_units)){
      drupal_set_message(t('Keine Einheiten in diesem Zeitraum verfügbar'),'error');
      depot_form_goto('ressourcen/'.$params['rid']);
    }

    if ($avail_units < $params['einheiten']){
      drupal_set_message(t('Achtung: Es sind in diesem Zeitraum nur @anzahl Einheit(en) verfügbar.',array('@anzahl' => $avail_units)), 'warning');
      $params['einheiten'] = $avail_units;
    }
  
    if (empty($ressource)){
      drupal_set_message(t('Dieses Angebot existiert nicht.'),'error');
      depot_form_goto('ressourcen');
    }

    if (isset($ressource['field_gemeinwohl']['und'][0]['value']) && $ressource['field_gemeinwohl']['und'][0]['value'] && !$user_is_organisation){
      drupal_set_message(t('Dieses Angebot steht nur für gemeinnützig anerkannte Organisationen zur Verfügung.'),'error');
      depot_form_goto('ressourcen/'.$params['rid']);
    }
  
    if (isset($ressource['field_minimale_anzahl']['und'][0]['value']) && $ressource['field_minimale_anzahl']['und'][0]['value'] > $params['einheiten']){
      drupal_set_message(t('Dieses Angebot ist erst ab einer Stückzahl von '.$ressource['field_minimale_anzahl']['und'][0]['value'].' reservierbar.'),'error');
      depot_form_goto('ressourcen/'.$params['rid']);
    }
    
    // Calculate price
    $preis = array();

    $preis['res_preis'] = (isset($ressource['field_gemeinwohl']['und'][0]['value']) && $ressource['field_gemeinwohl']['und'][0]['value']) ? $ressource['field_kosten_2']['und'][0]['value'] : $ressource['field_kosten']['und'][0]['value'];
    $preis['res_kaution'] = (isset($ressource['field_kaution']['und'][0]['value'])) ? $ressource['field_kaution']['und'][0]['value'] : 0;
    $preis['res_takt'] = $ressource['field_abrechnungstakt']['und'][0]['value'];
    $preis['res_tax'] = (isset($ressource['field_mwst']['und'])) ? $ressource['field_mwst']['und'][0]['value'] : 0;

    $preis['preis_plain'] = 0;
    $preis['preis_tax'] = 0;
    $preis['preis_kaution'] = 0;
    $preis['preis_total'] = 0;

    if ($preis['res_kaution'] > 0){
      $preis['preis_kaution'] = $preis['res_kaution'] * $params['einheiten'];
    }

    $preis['preis_plain'] = $preis['res_preis'] * $total_time * $params['einheiten'];

    if ($preis['res_tax'] > 0){
      $preis['preis_tax'] = $preis['preis_plain'] * $preis['res_tax'] / 100;
      $preis['preis_total'] = $preis['preis_plain'] + $preis['preis_tax'];
    } else {
      $preis['preis_total'] = $preis['preis_plain'];
    }

    $preis['preis_total'] += $preis['preis_kaution'];

  }

  $preis = machine_to_human_price($preis);

  // Add the field related form elements of bat_booking entity
  $form_state['bat_booking'] = $booking;

  // Build columns for header panel 
  $res_col = '
    <div class="medium-4 column">
      <h4><i class="fi fi-burst"></i>'.$ressource['name'].'</h4>';
  if ($anbieter->is_organisation){
    $res_col .= '<p>'.$anbieter->field_organisation_name['und'][0]['value'] .' ('. $anbieter->field_organisation_typ['und'][0]['value'] .')</p>';
  } else {
    $res_col .= '<p><strong>'.t('Anbieter').':</strong> '.$anbieter->field_anrede['und'][0]['value'].' '.$anbieter->field_vorname['und'][0]['value'].' '.$anbieter->field_nachname['und'][0]['value'].'</p>';
  }
  
  $res_col .= '<br /><p><strong>'.t('Abholort').':</strong> '.$ressource['field_adresse_strasse']['und'][0]['value'].' '.t('in').' '.$ressource['field_adresse_postleitzahl']['und'][0]['value'].' '.$ressource['field_adresse_ort']['und'][0]['value'].'</p>';
  $res_col .= '</div>';

  $time_col = '
    <div class="medium-4 column">
      <h4><i class="fi fi-calendar"></i>'. t('Zeitraum') .'</h4>
      <p>'. date_format($begin, 'd.m., H:i') .' bis '. date_format($end, 'd.m.Y, H:i') .' ('. $total_time_readable .')</p>
      <p>'.t('Anzahl gewählter Einheiten').': '.$params['einheiten'].'</p>
    </div>';

  $price_card_tooltip = '
    <p>'.t('Preis pro Einheit').': '. $preis['res_preis'] . '€</p>' 
    .($preis['res_kaution'] > 0 ? '<p>'.t('Kaution pro Einheit').': '. $preis['res_kaution'].'€</p>' : '')
    . '<hr /><p>'.t('x Gewählte Einheiten') .': '. $params['einheiten'].'</p>'
    . '<p>'.t('x Zeitraum').': '.$total_time_readable.'</p>';

  $price_col = '
  <div class="medium-4 column">
    <h4><i class="fi fi-price-tag"></i>'. t('Preis').'</h4>
    <i class="fi fi-info has-tip" data-tooltip aria-haspopup="true" title="'.$price_card_tooltip.'"></i>
     <p><strong>'. t('Netto') .'</strong>: '. $preis['preis_plain'] .'€</p>
     '. ($preis['preis_tax'] > 0 ? '<p><strong>'.t('MwSt').':</strong> '. $preis['preis_tax'] .'€</p>' : '') .'
     '. ($preis['preis_kaution'] > 0 ? '<p><strong>'.t('Kaution').':</strong> '.$preis['preis_kaution'].'€</p>' : '') .'      
     <hr /><p><strong>'. t('Brutto') .'</strong>: '. $preis['preis_total'] .'€</p>
    </div>';
  
  $status_col = null;

  if ($edit_mode){
    if ($booking->field_genehmigt['und'][0]['value'] == 0){
      $status_col = '<div id="booking-status">'.t('Buchungsstatus').': <strong>'. t('Ausstehend').'</strong></div>';
    } else {
      $status_col = '<div id="booking-status" class="genehmigt"><i class="fi fi-checkbox"></i> '.t('Buchungsstatus').': <strong>'.t('Akzeptiert').'</strong></div>';      
    }
    if ($user_is_anbieter){
      $status_col .= '<a href="/reservierungen/'.$booking->booking_id.'/change_status" class="float-right">'.t('Status ändern').'</a>';
    }
  }
  
  $form['buchung_header'] = array(
   '#markup' => '<div id="buchung-header" class="panel callout row"><div class="medium-12 column"><h5 class="left">'.t('Ihre Reservierung').'</h5>'.$status_col.'<hr /></div>'. $res_col . $time_col . $price_col . '</div>',
   '#weight' => -98
  );

  if (isset($_SESSION['detail_only_mode']) && $_SESSION['detail_only_mode']){
    $_SESSION['detail_only_mode'] = false;
    unset($_SESSION['detail_only_mode']);
    return $form;
  }

  $form['buchung_form_header'] = array(
    '#markup' => '<div class="medium-12 column"><h5>'.t('Ihre Daten').'</h5><hr /></div>',
    '#weight' => -97
  );

  field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL);  

  $form['booking_start_date']['#attributes']['class'][] = 'medium-6 column';
  $form['booking_start_date']['#access'] = $allow_change;
  $form['booking_start_date']['und'][0]['#title'] = t('Start Ausleihe');
  $form['booking_start_date']['und'][0]['#default_value']['value'] = $begin_bat;
  $form['booking_end_date']['#attributes']['class'][] = 'medium-6 column';
  $form['booking_end_date']['#access'] = $allow_change;
  $form['booking_end_date']['und'][0]['#title'] = t('Ende Ausleihe');
  $form['booking_end_date']['und'][0]['#default_value']['value'] = $end_bat;

  $form['field_preis']['und'][0]['#access'] = $allow_change;
  $form['field_preis']['und'][0]['value']['#default_value'] = $preis['preis_total'];
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
  
  $form['field_preis_meta']['#access'] = FALSE;
  $form['field_preis_meta']['und'][0]['value']['#default_value'] = serialize($preis);
  
  if (isset($user->field_vorname['und'][0]['value'])){
    $form['field_ausleiher_name']['und'][0]['value']['#default_value'] = $user->field_vorname['und'][0]['value'];
  }
  if (isset( $user->field_telefonnummer['und'][0]['value'])){
    $form['field_ausleiher_telefonnummer']['und'][0]['value']['#default_value'] = $user->field_telefonnummer['und'][0]['value'];
  }
  $form['field_ausleiher_email']['und'][0]['value']['#default_value'] = $user->mail;
  $form['field_ausleiher_name']['#attributes']['class'] = array('medium-4 column');
  $form['field_ausleiher_telefonnummer']['#attributes']['class'] = array('medium-4 column');
  $form['field_ausleiher_email']['#attributes']['class'] = array('medium-4 column');
  $form['field_geplante_nutzung']['#attributes']['class'] = array('column');
  $form['field_nachricht_an_den_anbieter']['#attributes']['class'] = array('column');
  $form['field_buchung_bedingungen']['#attributes']['class'] = array('column');
  $form['field_genehmigt']['#attributes']['class'] = array('column');
  $form['field_genehmigt']['#access'] = $allow_change;  
  // Meta's...
  $form['field_ausleiher']['#access'] = FALSE;
  $form['field_ausleiher']['und'][0]['value']['#default_value'] = $user->uid;
  $form['field_verleiher']['#access'] = FALSE;
  $form['field_verleiher']['und'][0]['value']['#default_value'] = $anbieter->uid;
  $form['field_name_ressource']['#access'] = false;
  $form['field_name_ressource']['und'][0]['value']['#default_value'] = $ressource['name'];
  $form['field_ressource_id']['#access'] = false;
  $form['field_ressource_id']['und'][0]['value']['#default_value'] = $params['rid'];
 
  $form['booking_event_reference']['#type'] = 'hidden';
  //print_r($form['booking_event_reference']['und'][0]); exit();
  $form['booking_event_reference']['und'][0]['target_id']['#default_value'] = 'Gebucht [state_id:30](0)';
  //dit-booking-event-reference-und-0-target-id
  
  // obsolet:
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
  $form['field_einheiten']['und']['#access'] = $allow_change;
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
    '#attributes' => array(
      'class' => array('button')
    )
  );
  if (!empty($booking->label) && bat_booking_access('delete', $booking)) {
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Buchung entfernen'),
      '#submit' => $submit + array('depot_booking_form_submit_delete'),
      '#weight' => 45,
    );
  } else {
    $form['actions']['cancel'] = array(
      '#markup' => l(t('Abbrechen & zurück'), 'ressourcen/'.$params['rid']),
      '#weight' => 50,
    );
  }

  $form_state['values']['params'] = $params;
  $form['#validate'][] = 'depot_booking_edit_form_validate';

  return $form;
}

/**
 * Form API validation callback for the booking form.
 */
function depot_booking_edit_form_validate(&$form, &$form_state) {
  entity_form_field_validate('bat_booking', $form, $form_state);
}

/**
 * Form API submit callback for the booking form.
 */
function depot_booking_edit_form_submit(&$form, &$form_state) {

  $params = array();
   __depot_booking_edit_form_set_params($params);

   depot_events_bulk_action($params['rid'], $params['einheiten'], $form['booking_start_date']['und'][0]['#default_value']['value'], $form['booking_end_date']['und'][0]['#default_value']['value']);


  $booking = entity_ui_controller('bat_booking')->entityFormSubmitBuildEntity($form, $form_state);
  $isNewBooking = true;

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

  if ($isNewBooking){
    /*
     depot:
     Setze Anzahl x units auf ausgebucht:
     Generiere Verleihvertrag
     Mail an Anbieter
     Mail an Interessent
    */
    depot_events_bulk_action($params['rid'], $params['einheiten'], $form['booking_start_date']['und'][0]['#default_value']['value'], $form['booking_end_date']['und'][0]['#default_value']['value']);
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
