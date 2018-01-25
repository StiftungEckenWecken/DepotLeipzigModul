<?php

function replace_euro($price){
  return str_replace(',','.',str_replace('€','',$price));
}

function depot_calc_price($ressource, $params, $total_time){
  
    $preis = array();
    $preis['res_preis'] = (float)(isset($ressource['field_gemeinwohl']['und'][0]['value']) && $ressource['field_gemeinwohl']['und'][0]['value']) ? replace_euro($ressource['field_kosten_2']['und'][0]['value']) : replace_euro($ressource['field_kosten']['und'][0]['value']);
    $preis['res_kaution'] = (float)(isset($ressource['field_kaution']['und'][0]['value'])) ? replace_euro($ressource['field_kaution']['und'][0]['value']) : 0;
    $preis['res_takt'] = $ressource['field_abrechnungstakt']['und'][0]['value'];
    $preis['res_tax'] = (int)(isset($ressource['field_mwst']['und'])) ? replace_euro($ressource['field_mwst']['und'][0]['value']) : 0;

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

    humanize_price($preis['res_preis']);
    humanize_price($preis['res_kaution']);
    humanize_price($preis['res_tax']);
    humanize_price($preis['preis_total']);
    humanize_price($preis['preis_plain']);
    humanize_price($preis['preis_tax']);
    humanize_price($preis['preis_kaution']);

    return $preis;
    
}

function __depot_booking_edit_form_set_params(&$params, $booking = null){

  if (isset($booking) && !empty($booking)){
    $params = array(
      'rid' => $booking->field_ressource_id['und'][0]['value'],
      'begin' => $booking->booking_start_date['und'][0]['value'],
      'end' => $booking->booking_end_date['und'][0]['value'],
      'einheiten' => $booking->field_einheiten['und'][0]['value']
    );
    return;
  }

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

  $force_header_only = (isset($booking->force_header_only));
  
  $user = user_load($user->uid);
  $edit_mode = (!empty($booking->booking_end_date));
  $params = array();
  $begin = null;
  $end = null;
  $form_state['bat_booking'] = $booking;

  if ($edit_mode){

    __depot_booking_edit_form_set_params($params, $booking);
    
    $begin = new DateTime($params['begin']);
    $end = new DateTime($params['end']);
    
    $preis = unserialize($booking->field_preis_meta['und'][0]['value']);
    
  } else {

    // Stick with BAT's required granularity
    // (minutes rounded to quarter steps)
    $round = 15*60;

    __depot_booking_edit_form_set_params($params);
    
    $begin = (new DateTime())->setTimestamp($params['begin']);
    $begin_formatted_hour = date('H',round(strtotime($begin->format('H:i')) / $round) * $round);
    $begin_formatted_min  = date('i',round(strtotime($begin->format('H:i')) / $round) * $round);
    $begin->setTime($begin_formatted_hour, $begin_formatted_min);

    $end = (new DateTime())->setTimestamp($params['end']);
    $end_formatted_hour = date('H',round(strtotime($begin->format('H:i')) / $round) * $round);
    $end_formatted_min  = date('i',round(strtotime($begin->format('H:i')) / $round) * $round);
    $end->setTime($end_formatted_hour, $end_formatted_min);
    
  }

  // 2. Manually check again for availability of resources
  $begin_bat = date_format($begin, 'Y-m-d H:i');
  $end_bat = date_format($end, 'Y-m-d H:i');

  $avail_units = count(depot_get_available_units_by_rid($params['rid'], $begin_bat, $end_bat, true));  

  $ressource = get_object_vars(bat_type_load($params['rid']));
  $user_is_organisation = in_array(ROLE_ORGANISATION_AUTH_NAME, $user->roles);
  $anbieter = user_load($ressource['uid']);
  $user_is_anbieter = ($anbieter->uid === $user->uid);  
  $anbieter->is_organisation = in_array(ROLE_ORGANISATION_NAME, $anbieter->roles);
  $allow_change = (user_has_role(ROLE_ADMINISTRATOR) || $edit_mode);  

  // Changes have to be appended further down (submit-edit-action)
  if ($ressource['field_abrechnungstakt']['und'][0]['value'] == 0){
    // granularity: daily
    $total_time = $begin->diff($end)->format('%a');
    if ($begin->format('Hi') < $end->format('Hi')){
      $total_time++;
    }
    if ($total_time == 0){
      $total_time = 1;
    }
    $total_time_readable = $total_time . ' ' . ($total_time == 1 ? t('Tag') : t('Tage'));
  } else {
    // hourly
    $total_time = $begin->diff($end)->format('%H:%I');
    $total_time_readable = $total_time . ' ' . t('Stunden');
  }
  
  if (!$edit_mode){

    if ($begin->format('Ymd') < date('Ymd')){
      drupal_set_message(t('Der gewählte Zeitraum muss in der Zukunft liegen.'),'error');
      depot_form_goto('ressourcen/'.$params['rid']);
    }
    
    if (empty($avail_units)){
      drupal_set_message(t('Keine Einheiten in diesem Zeitraum verfügbar'),'error');
      depot_form_goto('ressourcen/'.$params['rid']);
    }

    if ($avail_units < $params['einheiten']){
      if ($avail_units == 0){
        drupal_set_message(t('Achtung: Es sind in diesem Zeitraum keine Einheiten mehr verfügbar. Entschuldige die Unannehmlichkeiten.'));       
        depot_form_goto('ressourcen/'.$params['rid']);        
      }
      drupal_set_message(t('Achtung: Es sind in diesem Zeitraum nur @anzahl Einheit(en) verfügbar. Wir haben Deine Auswahl entsprechend angepasst.',array('@anzahl' => $avail_units)), 'warning');
      $params['einheiten'] = $avail_units;
    }
  
    if (empty($ressource)){
      drupal_set_message(t('Dieses Angebot existiert nicht (mehr).'),'error');
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
    
    $preis = depot_calc_price($ressource, $params, $total_time);

  }

  $preis = machine_to_human_price($preis);

  // Build columns for header panel 
  $res_col = '
    <div class="medium-4 column">
      <h4><i class="fi fi-burst"></i>'. ($force_header_only ? t('Ressource').': ' : '') . $ressource['name'].'</h4>';
  if ($anbieter->is_organisation && !$force_header_only){
    $res_col .= '<p>'.$anbieter->field_organisation_name['und'][0]['value'] .' ('. $anbieter->field_organisation_typ['und'][0]['value'] .')</p>';
  } else if (!$force_header_only) {
    $res_col .= '<p><strong>'.t('Anbieter').':</strong> '.$anbieter->field_anrede['und'][0]['value'].' '.$anbieter->field_vorname['und'][0]['value'].' '.$anbieter->field_nachname['und'][0]['value'].'</p>';
  }
  
  $res_col .= '<br /><p><strong>'.t('Abholort').':</strong> '.$ressource['field_adresse_strasse']['und'][0]['value'].' '.t('in').' '.$ressource['field_adresse_postleitzahl']['und'][0]['value'].' '.$ressource['field_adresse_ort']['und'][0]['value'].'</p>';
  $res_col .= '</div>';

  $time_col = '
    <div class="medium-4 column">
      <h4><i class="fi fi-calendar"></i>'. t('Zeitraum') .'</h4>
      <p>'. date_format($begin, 'd.m., H:i') .' '.t('bis').' '. date_format($end, 'd.m.Y, H:i') .' ('. $total_time_readable .')</p>
      <p>'.t('Anzahl gewählter Einheiten').': '.$params['einheiten'].'</p>
    </div>';

  $price_card_tooltip = '
    <p>'.t('Preis pro Einheit / Tag').': '. $preis['res_preis'] . '</p>' 
    .($preis['res_kaution'] > 0 ? '<p>'.t('Kaution pro Einheit').': '. $preis['res_kaution'].'</p>' : '')
    . '<hr /><p>'.t('x Gewählte Einheiten') .': '. $params['einheiten'].'</p>'
    . '<p>'.t('x Zeitraum').': '.$total_time_readable.'</p>';

    $price_col = '
  <div class="medium-4 column aside-price">
    <h4><i class="fi fi-price-tag"></i>'. t('Preis').'</h4>
    <i class="fi fi-info has-tip" data-tooltip aria-haspopup="true" title="'.$price_card_tooltip.'"></i>
     <p><strong>'. t('Netto') .'</strong>: '. $preis['preis_plain'] .'</p>
     '. ($preis['preis_tax'] > 0 ? '<p><strong>'.t('MwSt').' ('. $ressource['field_mwst']['und'][0]['value'] .'%):</strong> '. $preis['preis_tax'] .'</p>' : '') .'
     '. ($preis['preis_kaution'] > 0 ? '<p><strong>'.t('Kaution').':</strong> '.$preis['preis_kaution'].'</p>' : '') .'      
     <hr /><p><strong>'. t('Gesamt') .'</strong>: '. $preis['preis_total'] .'</p>
    </div>';
  
  $status_col = null;

  if ($edit_mode && !$force_header_only){
    if ($booking->field_genehmigt['und'][0]['value'] == 0){
      $status_col = '<div id="booking-status">'.t('Buchungsstatus').': <strong>'. t('Ausstehend').'</strong></div>';
    } else {
      $status_col = '<div id="booking-status" class="genehmigt"><i class="fi fi-checkbox"></i> '.t('Buchungsstatus').': <strong>'.t('Akzeptiert').'</strong></div>';      
    }
    if ($user_is_anbieter && !$booking->field_genehmigt['und'][0]['value']){
      $status_col .= '<a href="/reservierungen/'.$booking->booking_id.'/change_status" class="right" style="padding-right:10px;"><i class="fi fi-check"></i> '.t('Buchung akzeptieren').'</a>';
    }
    if ($booking->field_genehmigt['und'][0]['value'] && isset($ressource['field_verleihvertrag_']['und'][0]['value']) && $ressource['field_verleihvertrag_']['und'][0]['value']){
      $status_col .= '<a href="/reservierungen/'.$booking->booking_id.'/verleihvertrag" class="right" style="padding-right:10px;">'.t('Verleihvertrag').'</a>';      
    }
  }
  
  $form['buchung_header'] = array(
   '#markup' => '<div id="buchung-header" class="panel callout row"><div class="medium-12 column"><h5 class="left">'.t('Ihre Reservierung').'</h5>'.$status_col.'<hr /></div>'. $res_col . $time_col . $price_col . '</div>',
   '#weight' => -98
  );

  $deleteButtonConfirm = "javascript:if(confirm('".t('Bist Du sicher? Gelöschte Reservierungen können nicht mehr rückgängig gemacht werden.')."')){return true;}else{return false;}";

  $deleteButton = array(
    '#markup' => '<a class="secondary button" onclick="'.$deleteButtonConfirm.'" href="/reservierungen/'.$booking->booking_id.'/delete">'.t('Buchung unwiderruflich stornieren').'</a>',
    '#weight' => 45,
  );

  $datesShow = false;
  $datesAllowEdit = false;
  
  if ($force_header_only || $edit_mode){
    if ($force_header_only || !$user_is_anbieter){

      field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL, array('field_name' => 'field_geplante_nutzung'));  
      field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL, array('field_name' => 'field_nachricht_an_den_anbieter'));  
      field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL, array('field_name' => 'field_ausleiher_name'));  
      field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL, array('field_name' => 'field_ausleiher_telefonnummer'));  
      field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL, array('field_name' => 'field_ausleiher_email'));  
      $form['field_geplante_nutzung']['#disabled'] = TRUE;
      $form['field_nachricht_an_den_anbieter']['#disabled'] = TRUE;
      $form['field_ausleiher_name']['#disabled'] = TRUE;
      $form['field_ausleiher_telefonnummer']['#disabled'] = TRUE;
      $form['field_ausleiher_email']['#disabled'] = TRUE;
      $form['field_ausleiher_name']['#attributes']['class'] = array('medium-4 column');
      $form['field_ausleiher_telefonnummer']['#attributes']['class'] = array('medium-4 column');
      $form['field_ausleiher_email']['#attributes']['class'] = array('medium-4 column');
      $form['actions'] = array(
        '#type' => 'actions',
        '#tree' => FALSE,
      );
      $form['actions']['delete'] = $deleteButton;
      
      return $form;

    } else {
      $datesShow = true;

      if ($booking->field_genehmigt['und'][0]['value'] == 0){
        $datesAllowEdit = true;
      } 
    }
  }

  $form['buchung_form_header'] = array(
    '#markup' => '<div class="medium-12 column"><h5>'.t('Ihre Daten').'</h5><hr /></div>',
    '#weight' => -97
  );

  field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL);  

  if ($edit_mode){
    $form['field_geplante_nutzung']['#disabled'] = TRUE;      
    $form['field_nachricht_an_den_anbieter']['#disabled'] = TRUE;
    $form['field_buchung_bedingungen']['#access'] = FALSE;
    $form['field_ausleiher_name']['#disabled'] = TRUE;
    $form['field_ausleiher_telefonnummer']['#disabled'] = TRUE;
    $form['field_ausleiher_email']['#disabled'] = TRUE;
  } else {
    $form['field_laenge']['und'][0]['value']['#default_value'] = $total_time_readable;
    if (isset($user->field_vorname['und'][0]['value'])){
      $form['field_ausleiher_name']['und'][0]['value']['#default_value'] = $user->field_vorname['und'][0]['value'];
    }
    if (isset($user->field_telefonnummer['und'][0]['value'])){
      $form['field_ausleiher_telefonnummer']['und'][0]['value']['#default_value'] = $user->field_telefonnummer['und'][0]['value'];
    }
    $form['field_ausleiher_email']['und'][0]['value']['#default_value'] = $user->mail;
  }

  $datesAllowEdit = true;

  $form['booking_start_date']['#attributes']['class'][] = 'medium-6 column'.(!$datesShow ? ' hide' : '');
  $form['booking_start_date']['#disabled'] = !$datesAllowEdit;
  $form['booking_start_date']['und'][0]['#title'] = t('Start Ausleihe');
  $form['booking_start_date']['und'][0]['#default_value']['value'] = $begin_bat;
  if (!$datesAllowEdit){
    $form['booking_start_date']['und'][0]['#description'] = t('Unveränderbar, da Reservierung bereits bestätigt');
  }
  $form['booking_end_date']['#attributes']['class'][] = 'medium-6 column'.(!$datesShow ? ' hide' : '');
  $form['booking_end_date']['#disabled'] = !$datesAllowEdit;
  $form['booking_end_date']['und'][0]['#title'] = t('Ende Ausleihe');
  $form['booking_end_date']['und'][0]['#default_value']['value'] = $end_bat;

  $options = array();

  // $result[''] = '<'.t('Alle').'>';
  for ($i = 1; $i <= ($ressource['field_anzahl_einheiten']['und'][0]['value']); $i++){
    $options[$i] = $i;
  }

  //$form['field_einheiten']['#default_value'] = $params['einheiten'];
  $form['field_einheiten']['und']['#attributes']['class'][] = 'medium-12 column';
  $form['field_einheiten']['und']['#description'] .= '. <strong>Achtung:</strong> Eine nachträgliche Erhöhung der Einheiten kann Überbuchungen zur Folge haben'; 
  $form['field_einheiten']['und']['#disabled'] = !$datesAllowEdit;
  $form['field_einheiten']['und']['#access'] = $datesShow;
  $form['field_einheiten']['und']['#options'] = $options;
  $form['field_einheiten']['und']['#default_value'] = array($params['einheiten']);

  $form['field_preis']['und'][0]['#access'] = false; //$allow_change;
  $form['field_preis']['und'][0]['value']['#default_value'] = $preis['preis_total'];
 
  // BAT Altlasten / entity functionality
  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => 99,
  );

  $access = false;//user_has_role(ROLE_ADMINISTRATOR); // || user_access('bypass bat_booking entities access')

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
  
  $form['field_ausleiher_name']['#attributes']['class'] = array('medium-4 column');
  $form['field_ausleiher_telefonnummer']['#attributes']['class'] = array('medium-4 column');
  $form['field_ausleiher_email']['#attributes']['class'] = array('medium-4 column');
  $form['field_geplante_nutzung']['#attributes']['class'] = array('column');
  $form['field_nachricht_an_den_anbieter']['#attributes']['class'] = array('column');
  $form['field_buchung_bedingungen']['#attributes']['class'] = array('column');
  $form['field_genehmigt']['#attributes']['class'] = array('column');
  $form['field_genehmigt']['#access'] = (user_has_role(ROLE_ADMINISTRATOR));
  $form['field_laenge']['#access'] = FALSE;

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
  $form['booking_event_reference']['und'][0]['target_id']['#default_value'] = 'Gebucht [state_id:30](0)';
  
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
    '#value' => ($edit_mode ? t('Buchung ändern') : t('Buchung anfragen')),
    '#submit' => $submit + array('depot_booking_edit_form_submit'),
    '#attributes' => array(
      'class' => array('button')
    )
  );
  if (!empty($booking->label) && bat_booking_access('delete', $booking)) {
    $form['actions']['delete'] = $deleteButton;
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

  global $user;
  global $base_url;

  $buchung_header = $form['buchung_header']['#markup'];
  
  $booking = entity_ui_controller('bat_booking')->entityFormSubmitBuildEntity($form, $form_state);
  $isNewBooking = empty($booking->label);
  $params = array();
  
  if ($isNewBooking){
    __depot_booking_edit_form_set_params($params);  
  } else {
    __depot_booking_edit_form_set_params($params, $booking);      
  }
  $ressource = get_object_vars(bat_type_load($params['rid']));
  $anbieter = user_load($ressource['uid']);

  $booking->created = !empty($booking->date) ? strtotime($booking->date) : REQUEST_TIME;
  $booking->changed = time();

  if (isset($booking->author_name) && $isNewBooking) {
    if ($account = user_load_by_name($booking->author_name)) {
      $booking->uid = $account->uid;
    } else {
      $booking->uid = 0;
    }
  }

  $begin = $booking->booking_start_date['und'][0]['value'];
  $end   = $booking->booking_end_date['und'][0]['value'];

  if ($isNewBooking){

    $booking->save();

    depot_events_bulk_action('add', $params['rid'], $params['einheiten'], $begin, $end, 'gebucht', 'Reservierung Nutzer '.$user->name, $booking->booking_id);

    $mail_body = "Lieber Anbieter beim Depot Leipzig,\r\n\r\n";
    $mail_body .= "Der Nutzer ".$user->name." hat folgende Reservierungsanfrage über das Depot gestellt:\r\n\r\n";
    $mail_body .= $buchung_header."\r\n\r\n";
    $mail_body .= "Genannter Grund für die Buchung: '".$form['field_geplante_nutzung']['und'][0]['value']['#value']."'.\r\n\r\n";
    
    if (!empty($form['field_nachricht_an_den_anbieter']['und'][0]['value']['#value'])){
      $mail_body .= "Zudem hat der Interessent folgende Nachricht hinterlassen: '".$form['field_nachricht_an_den_anbieter']['und'][0]['value']['#value']."'.\r\n\r\n";
    }

    $mail_body .= "Diese und alle weiteren Details lassen sich über folgenden Link einsehen und ggf. bearbeiten: ". $base_url ."/reservierungen/".$booking->booking_id."/edit\r\n\r\n";
    $mail_body .= "Bist Du mit der Reservierung einverstanden, vergiss bitte nicht, diese auch zeitnah zu genehmigen, damit der Nutzer hierüber vom Depot Leipzig informiert";
    
    if (isset($ressource['field_verleihvertrag_']['und'][0]['value']) && $ressource['field_verleihvertrag_']['und'][0]['value']){
      $mail_body .= " und, wie gewünscht, ein Verleihvertrag erstellt";
    }

    $mail_body .= " werden kann. Den Link zum aktivieren findest Du unter 'Mein Depot' oder direkt unter ".$base_url."/reservierungen/".$booking->booking_id."/change_status\r\n\r\n";
    $mail_body .= "Vielen Dank für Dein Interesse, das Team vom Depot Leipzig";
    
    $mail_body = str_replace('Ihre Reservierung','',$mail_body);

    $mail_params = array(
      'body' => strip_tags($mail_body,'<p><h4><br><br><div>'),
      'subject' => t('Depot Leipzig: Reservierungsanfrage'),
    );
  
    drupal_mail('depot','depot_buchung_form', $anbieter->mail,'de', $mail_params);
    drupal_set_message(t('Ihre Reservierung wurde gespeichert und der Ressourceninhaber informiert. Dieser wird sich mit Ihnen in Verbindung setzen.'));    
  
  } else {

    $begin_dt = new DateTime($begin);
    $end_dt   = new DateTime($end);

    if ($ressource['field_abrechnungstakt']['und'][0]['value'] == 0){
      // granularity: daily
      $total_time = $begin_dt->diff($end_dt)->format('%a');
      if ($begin_dt->format('Hi') < $end_dt->format('Hi')){
        $total_time++;
      }
      if ($total_time == 0){
        $total_time = 1;
      }
      $total_time_readable = $total_time . ' ' . ($total_time == 1 ? t('Tag') : t('Tage'));
    } else {
      // hourly
      $total_time = $begin_dt->diff($end_dt)->format('%H:%I');   
      $total_time_readable = $total_time . ' ' . t('Stunden');
    }

    $booking->field_laenge['und'][0]['value'] = $total_time_readable;

    // Eventually, units and date changed -> recalc price
    // TODO: Change after anti-over-booking-feature has been implemented
    $ressource = get_object_vars(bat_type_load($params['rid']));
    $preis = depot_calc_price($ressource, $params, $total_time);
    $preis = machine_to_human_price($preis);

    $booking->field_preis['und'][0]['value'] = $preis['preis_total'];    
    $booking->field_preis_meta['und'][0]['value'] = serialize($preis);
    $booking->save(); // Append price changes

    depot_events_bulk_action('edit', $params['rid'], $params['einheiten'], $begin, $end, 'gebucht', 'Reservierung Nutzer '.$user->name, $booking->booking_id);
    drupal_set_message(t('Die Reservierung wurde erfolgreich bearbeitet. Dabei wurde ggf. ein neuer Gesamtpreis kalkuliert.'));
  
  }

  $form_state['redirect'] = 'mein-depot/reservierungen';
  
}
