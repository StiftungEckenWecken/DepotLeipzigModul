<?php

/**
 * Form callback wrapper: delete a ressource.
 */
function depot_ressource_delete_form_wrapper($type) {
  return drupal_get_form('depot_ressource_delete_form', $type);
}

/**
 * Form callback: create or edit a ressource.
 */
function depot_ressource_edit_form($form, &$form_state, $type) {
  
  global $user;

  $user = user_load($user->uid);

  $access = false;

  $form['intro'] = array(
    '#markup' => '<p>'.t('Alle mit <span style="color:red;">*</span> markierten Felder sind auszufüllen. Verfügbarkeiten können im nächsten Schritt verwaltet werden.').'</p>',
    '#weight' => '-99'
  );

  $form['#attributes']['class'][] = 'depot-edit-resource-form';

  $form['type'] = array(
    '#type' => 'value',
    '#value' => $type->type,
  );

  $form_state['was_active_before'] = (bool) $type->field_aktiviert['und'][0]['value'];

  // Add the default field elements.
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Name der Ressource'),
    '#default_value' => isset($type->name) ? $type->name : '',
    '#maxlength' => 255,
    '#required' => TRUE,
    '#weight' => -98,
    '#prefix' => '<div class="medium-6 column left">',
    '#suffix' => '</div>'
  );

  // Add the field related form elements.
  $form_state['bat_type'] = $type;

  field_attach_form('bat_type', $type, $form, $form_state, entity_language('bat_type', $type));
  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => 99,
  );

  $form['field_anzahl_einheiten']['#attributes']['class'][] = 'medium-6 column';
  //$form['field_anzahl_einheiten']['#weight'] = 6;
  $form['field_minimale_anzahl']['#attributes']['class'][] = 'medium-6 column';
  
  // Custom field autoselector based on select2.js
  // Hide actual kategorie fields, select them via depot.js
  $form['field_kategorie']['#attributes']['class'][] = 'medium-6 column right hide';
  
  $form['field_fake_kategorie'] = array();
  $selectListKategorien = array();

  $typeKategorien = (isset($type->field_kategorie['und'][0]['state_id']) ? $type->field_kategorie['und'] : '');
  // @todo critical : []
  
  foreach (bat_event_get_states('depot_kategorie') as $kategorie) {
    // Format as required by form API
    $autocompleteVal = $kategorie['label']. ' [state_id:'.$kategorie['id'].']';
    $selectListKategorien[$autocompleteVal] = $kategorie['label']; 

    foreach ($typeKategorien as $_kategorie) {
      if ($_kategorie['state_id'] == $kategorie['id']) {
        $form['field_fake_kategorie']['#default_value'][] = $autocompleteVal;
      }
    }
  };

  natcasesort($selectListKategorien);

  $form['field_fake_kategorie']['#type'] = 'select';
  $form['field_fake_kategorie']['#selected'] = TRUE;
  $form['field_fake_kategorie']['#required'] = FALSE;
  $form['field_fake_kategorie']['#multiple'] = TRUE;
  $form['field_fake_kategorie']['#title'] = t('Kategorie');   
  $form['field_fake_kategorie']['#options'] = $selectListKategorien;
  $form['field_fake_kategorie']['#prefix'] = '<div class="medium-6 column">';
  $form['field_fake_kategorie']['#suffix'] = '</div>';

  $form['field_bild_i']['#prefix'] = '<fieldset class="medium-12 column"><legend>'.t('Bilder').'</legend><div class="medium-4 column">';
  $form['field_bild_i']['#suffix'] = '</div>';
  $form['field_bild_ii']['#prefix'] = '<div class="medium-4 column">';
  $form['field_bild_ii']['#suffix'] = '</div>';
  $form['field_bild_ii']['und'][0]['#description'] = '';
  $form['field_bild_iii']['#prefix'] = '<div class="medium-4 column">';
  $form['field_bild_iii']['#suffix'] = '</div></fieldset>';
  $form['field_bild_iii']['und'][0]['#description'] = '';

  $form['field_kosten_2']['#prefix'] = '<fieldset class="medium-12 column"><legend>'.t('Preis').'</legend><div class="medium-3 column">';
  $form['field_kosten_2']['#suffix'] = '</div>';
  $form['field_kosten']['#prefix'] = '<div class="medium-3 column">';
  $form['field_kosten']['#suffix'] = '</div>';
  $form['field_kaution']['#prefix'] = '<div class="medium-2 column">';
  $form['field_kaution']['#suffix'] = '</div>';
  $form['field_mwst']['#prefix'] = '<div class="medium-2 column">';
  $form['field_mwst']['#suffix'] = '</div>';
  $form['field_abrechnungstakt']['#prefix'] = '<div class="medium-2 column">';
  $form['field_abrechnungstakt']['#suffix'] = '</div></fieldset>';

  $form['field_adresse_strasse']['#prefix'] = '<fieldset class="medium-12 column"><legend>'.t('Ort der Abholung').'</legend><div class="medium-5 column">';
  $form['field_adresse_strasse']['#suffix'] = '</div>';
  $form['field_adresse_strasse']['#default_value'] = $user->field_user_adresse_strasse['und'][0]['safe_value'];
  $form['field_adresse_postleitzahl']['#prefix'] = '<div class="medium-2 column">';
  $form['field_adresse_postleitzahl']['#suffix'] = '</div>';
  $form['field_adresse_postleitzahl']['#default_value'] = $user->field_user_adresse_postleitzahl['und'][0]['safe_value'];
  $form['field_adresse_ort']['#prefix'] = '<div class="medium-3 column">';
  $form['field_adresse_ort']['#suffix'] = '</div>';
  $form['field_adresse_ort']['#default_value'] = $user->field_user_adresse_wohnort['und'][0]['safe_value'];
  $form['field_bezirk']['#prefix'] = '<div class="medium-4 column">';
  $form['field_bezirk']['#suffix'] = '</div>';
  $form['field__ffnungszeiten']['#suffix'] = '</fieldset>';

  // Altlast v1:
  $form['field_bezirk']['#type'] = 'hidden';

  /*$form['uploads'] = array(
    '#type' => 'container',
    '#tree' => true,
    '#weight' => 17,
    '#prefix' => '<fieldset class="medium-12 column fieldset-toggle'.(!empty($type->name) || true ? ' toggled' : '').'"><legend>'.t('Links, Anhänge & Textvorlagen').'</legend>',
    '#suffix' => '</fieldset>'
  );
  $form['uploads']['link_i'] = $form['field_links_i'];
  $form['uploads']['link_ii'] = $form['field_links_ii'];
  $form['uploads']['link_iii'] = $form['field_links_iii'];
  $form['uploads']['upload_i'] = $form['field_upload_i'];
  $form['uploads']['upload_ii'] = $form['field_upload_ii'];*/ // Below you could add fieldset-toggle to enable toggling
  $form['field_links_i']['#prefix'] = '<fieldset class="medium-12 column '.(!empty($type->name) || true ? ' toggled' : '').'"><legend>'.t('Links, Anhänge & Textvorlagen').'</legend>';
  $form['field_upload_i']['#prefix'] = '<div class="medium-6 column">';
  $form['field_upload_i']['#suffix'] = '</div>';
  $form['field_upload_ii']['#prefix'] = '<div class="medium-6 column">';
  $form['field_upload_ii']['#suffix'] = '</div><hr />';
  $form['field_upload_ii']['und'][0]['#description'] = '';
  $form['field_text_buchungsbes_tigung']['#suffix'] = '</fieldset>';

  $form['field_aktiviert']['#attributes']['class'][] = 'small-12 column';
  $form['field_slug']['#attributes']['class'][] = 'small-12 column';
  $form['field_adresse_longitude']['#attributes']['class'][] = 'small-12 column';
  $form['field_adresse_latitude']['#attributes']['class'][] = 'small-12 column';

  $form['field_beschreibung']['#attributes']['class'][] = 'small-12 column';
  $form['field_gemeinwohl']['#attributes']['class'][] = 'small-12 column';
  $form['field_nutzungsbedingungen']['#attributes']['class'][] = 'small-12 column';

  if (!depot_admin_access_only()){
    $form['field_adresse_longitude']['#access'] = FALSE;
    $form['field_adresse_latitude']['#access'] = FALSE;
    $form['field_aktiviert']['#access'] = FALSE;
    $form['field_slug']['#access'] = FALSE;
  } else {
    // Force visibility
    $form['field_adresse_longitude']['#access'] = TRUE;
    $form['field_adresse_latitude']['#access'] = TRUE;
    $form['field_aktiviert']['#access'] = TRUE;
    $form['field_slug']['#access'] = TRUE;
  }

 # if ($formfield_verleihvertrag_)

  //$form['field_links_i']['und'][0]['#title'] = '<i class="fi fi-link"></i>';
  if (empty($type->field_links_i)) {
    $form['field_links_ii']['#attributes']['class'][] = 'hide';
  }
  if (empty($type->field_links_ii)) {
    $form['field_links_iii']['#attributes']['class'][] = 'hide';
  }

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
    '#value' => t('Ressource speichern'),
    '#submit' => $submit + array('depot_ressource_edit_form_submit_wrapper'),
  );

  $form['actions']['submit']['#attributes']['class'] = array('button');
  $form['actions']['submit']['#attributes']['class'] = array('button expand margin-top-ten');
  
  // If !new form, add more options
  if (!empty($type->name)) {

    $form['field_agb']['#type'] = 'hidden';

    /*$form['actions']['availabilities'] = array(
      '#markup' => '<a href="#" title="'.t('Verfügbarkeiten bearbeiten').'" class="button"><fi class="fi fi-calendar"></i> '.t('Verfügbarkeiten bearbeiten').'</a>',
    );*/

    /*$form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Ressource löschen'),
      '#suffix' => l(t('Abbrechen'), 'ressourcen'),
      '#submit' => $submit + array('depot_ressource_form_submit_delete'),
      '#weight' => 45,
    );*/
  } else {
    $form['actions']['submit']['#attributes']['class'] = array('button expand margin-top-ten');
  }

  //$form['#validate'][] = 'depot_ressource_edit_form_validate';

  return $form;
}

/**
 * Form API validate callback for the booking type form.
 */
function depot_ressource_edit_form_validate(&$form, &$form_state) {

  entity_form_field_validate('bat_type', $form, $form_state);
  //print_r($form_state); exit(); 
  // min. Ressourcen < Anzahl Einheiten?
  // URL bei links richtig?
}

/**
 * Form API submit callback for the type form.
 */
function depot_ressource_edit_form_submit(&$form, &$form_state) {
   
  global $user;
  global $base_url;

  $rp = depot_get_active_regionalpartner();

  $type = entity_ui_controller('bat_type')->entityFormSubmitBuildEntity($form, $form_state);
  $type->created = !empty($type->date) ? strtotime($type->date) : REQUEST_TIME;

  $newEntity = (empty($type->type_id));

  if (!$newEntity) {

    $type->changed = time();

  }
  
  if ($newEntity && isset($type->author_name)) {
    
    if ($account = user_load_by_name($type->author_name)) {
      $type->uid = $account->uid;
      watchdog('Changed uid in first line to '.$type->uid.' for Ressource '.$type->type_id, 'alert');
    }
    else {
      $type->uid = 0;
    }
    watchdog('Changed uid to '.$type->uid.' for Ressource '.$type->type_id, 'alert');
  
  }

  humanize_price($type->field_kosten['und'][0]['value']);
  humanize_price($type->field_kosten_2['und'][0]['value']);
  humanize_price($type->field_kaution['und'][0]['value']);

  $type->save();

  if ($newEntity) {
    // @todo Check first if adress did change at all

    try {

      $wrapper = entity_metadata_wrapper('bat_type', $type);

      // Generate geodata
      $geocoder_response = depot_resource_geocodify($wrapper);
      
      if (isset($geocoder_response->Response) && count($geocoder_response->Response->View) >= 1) {
          
        $location = $geocoder_response->Response->View[0]->Result[0]->Location;
        $wrapper->field_adresse_latitude = $location->DisplayPosition->Latitude; 
        $wrapper->field_adresse_longitude = $location->DisplayPosition->Longitude; 
        
      }
    } catch (Exception $e) {
      
      if (depot_admin_access_only()) {
        drupal_set_message('Administrator-Hinweis: Konnte keine Geodaten ermitteln.','warning');
      }

      watchdog('Konnte keine Geodaten ermitteln - Ressource Name: '. $type->name,'alert');

    }

    // Generate url slug
    // !!! Bezirke temporary deactivated till required again in depot v2.x

    //$bezirke = bat_event_get_states('depot_bezirk');

    $slug = slugify($wrapper->name->value()) . '-' . $wrapper->field_adresse_postleitzahl->value();

    while (depot_resource_slug_exists($slug)) {
      // Ooops - same slug already exists -> modify this one
      $slug .= '-1';
    }
   
    /*if (isset($bezirke[$wrapper->field_bezirk->value()['state_id']])) {
      $slug .= '-' . slugify($bezirke[$wrapper->field_bezirk->value()['state_id']]['label']);
    }*/

    $wrapper->field_slug = $slug;
    $wrapper->save();

    depot_units_bulk_action('add', $type->name, $type->type_id, $form_state['values']['field_anzahl_einheiten']['und'][0]['value']);
    drupal_set_message(t('Ressource "@name" wurde gespeichert und wartet nun auf Aktivierung. Sie können Sperrzeiten jederzeit unter "Verfügbarkeiten ändern" festlegen.', array('@name' => $type->name)));    
    
    $mail_body = "Lieber Administrator,\r\n\r\n";
    $mail_body .= "Die depot-NutzerIn ".$user->name." hat die Ressource ".$type->name." eingestellt\r\n";
    $mail_body .= "Diese ist unter ".$base_url."/ressourcen/".$slug." zu finden.\r\n";
    $mail_body .= "Um die Ressource freizuschalten, gehen Sie bitte auf 'Ressource bearbeiten' und setzen Sie ein Häckchen bei 'Genehmigt'. Die NutzerIn wird daraufhin verständigt.";
    
    $params = array(
      'body' => $mail_body,
      'subject' => t('depot @name: Ressource wartet auf Freischaltung', array('@name' => $rp['region']['name'])),
    );
  
    drupal_mail('depot','depot_ressource_form',variable_get('site_mail', ''),'de',$params);
  
  } else {

    if (!$form_state['was_active_before'] && depot_admin_access_only() && $form_state['values']['field_aktiviert']['und'][0]['value']) {
      // Was not activated, now it is!
      $depot_user = user_load($type->uid);

      $mail_body = "Liebe depot-NutzerIn,\r\n\r\n";
      $mail_body .= "Deine Ressource *".$type->name."* wurde durch das depot-Team freigeschaltet und steht nun im Web zur Buchung bereit.\r\n\r\n";
      $mail_body .= "Vielen Dank, dass Du die Ressource online zur Mitnutzung bereitgestellt hast. Wir hoffen, viele nette Leute werden sie mit Dir teilen.\r\n\r\n";
      $mail_body .= "Hast Du Fragen oder Anregungen zum depot? Hier hilft Dir die kleine Bedienungsanleitung (https://depot.social/so-funktionierts) oder die Antworten auf häufig gestellte Fragen (https://depot.social/faq) weiter. Für Anregungen oder offene Fragen zögere nicht, uns diese über das Kontaktformular unter https://". $rp['domain'] ."/contact mitzuteilen.";
      $mail_body .= "Viele Grüße,\r\nDein Team vom depot " . $rp['region']['name'];

      $params = array(
        'body' => $mail_body,
        'subject' => t('depot @name: Ressource '.$type->name.' wurde freigeschaltet', array('@name' => $rp['region']['name'])),
      );
    
      drupal_mail('depot','depot_ressource_form',$depot_user->mail,'de',$params);  
      drupal_set_message(t('NutzerIn wurde über Genehmigung benachrichtigt.'));

    }

    depot_units_bulk_action('edit', $type->name, $type->type_id, $form_state['values']['field_anzahl_einheiten']['und'][0]['value']);    
    
    drupal_set_message(t('Ressource "@name" wurde aktualisiert.', array('@name' => $type->name)));

  }

  $form_state['redirect'] = 'ressourcen/'. $type->field_slug['und'][0]['value'];
}  

/**
 * Form API submit callback for the delete button.
 */
function depot_ressource_form_submit_delete(&$form, &$form_state) {
  $destination = array();
  if (isset($_GET['destination'])) {
    $destination = drupal_get_destination();
    unset($_GET['destination']);
  }
  // TODO: Redirect to /ressourcen
  $form_state['redirect'] = array('admin/bat/config/types/manage/' . $form_state['bat_type']->type_id . '/delete?destination=/ressourcen', array('query' => $destination));
}

/**
 * Form callback: confirmation form for deleting a Type.
 *
 * @param $type
 *   The Type entity to delete.
 *
 * @see confirm_form()
 */
function depot_ressource_delete_form($form, &$form_state, $type) {
 
  $form_state['bat_type'] = $type;
  $form['#submit'][] = 'depot_ressource_delete_form_submit';
  $form = confirm_form($form,
    t('Sicher, dass Sie die Ressource %name löschen möchten?', array('%name' => $type->name)),
    'admin/bat/config/types/manage',
    '<p>' . t('Hierbei werden alle damit verbundenen Daten (bspw. Buchungen) entfernt') . '</p>',
    t('Löschen'),
    t('Cancel'),
    'confirm'
  );
  return $form;
}

/**
 * Submit callback for type_delete_form.
 */
function depot_ressource_delete_form_submit($form, &$form_state) {
  $type = $form_state['bat_type'];
  bat_type_delete($type);
  drupal_set_message(t('Ressource %name wurde gelöscht.', array('%name' => $type->name)));
  watchdog('bat', 'Deleted Type %name.', array('%name' => $type->name));
  $form_state['redirect'] = 'ressourcen';
}