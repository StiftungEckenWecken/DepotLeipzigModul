<?php

// FORM 1

/* Act as an organization, not person */
function depot_user_organisation_form($form, &$form_state) {

  global $user;
  $user = user_load($user->uid);

  $intro_text = '<h3>'.t('Als Organisation auftreten').'</h3>';
  $intro_text .= '<p>'.t('Möchten Sie im Depot nicht als Einzelperson behandelt werden, können Sie dies hier tun.').'</p>';
  $is_organisation = user_has_role(ROLE_ORGANISATION);
  $is_organisation_auth = user_has_role(ROLE_ORGANISATION_AUTH);  

  if ($is_organisation_auth){
    $intro_text .= '<p>'.t('<strong>Gemeinwohlanerkennung: <span style="color:green;">Aktiv</span></strong></p><hr />').'</p>';
  } else {
    $intro_text .= '<p>'.t('<strong>Gemeinwohlanerkennung: <span style="color:red;">Inaktiv</span></strong></p><hr />').'</p>';
  }

  $form['intro'] = array(
    '#markup' => $intro_text,
    '#weight' => '-99'
  );

  $form['organisation_ja'] = array(
    '#type' => 'checkbox',
    '#title' => t('Als Organisation auftreten'),
    '#default_value' => 1,
    '#disabled' => $is_organisation
  );

  $form['organisationstyp'] = array(
    '#type' => 'select',
    '#title' => t('Organisationstyp'),
    '#options' => array(
      'stiftung' => t('Stiftung'),
      'verein' => t('Verein'),
      'unternehmen' => t('Unternehmen'),
      'sontiges' => t('Sonstiges'),
    ),
    '#disabled' => $is_organisation,
    '#default_value' => $user->field_organisation_typ['und'][0]['value']
  );

  $form['name'] = array(
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#disabled' => $is_organisation,
    '#default_value' => $user->field_organisation_name['und'][0]['value']
  );

  $form['website'] = array(
    '#title' => t('Website'),
    '#type' => 'textfield',
    '#required' => FALSE,
    '#disabled' => $is_organisation,
    '#default_value' => $user->field_organisation_website['und'][0]['value']
  );
  
  if (!$is_organisation){
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Beantragen',
      '#attributes' => array(
        'class' => array('button expand')
      ),
      '#submit' => array('depot_user_organisation_form_submit'),
    );
  }

  return $form;

}

/**
 * Form API submit callback for FORM 1
 */
function depot_user_organisation_form_submit(&$form, &$form_state) {

  global $user;
  $user = user_load($user->uid);
  global $base_url;

  $userEdit = array(
    'field_organisation_typ' => array(
      'und' => array(
        0 => array(
          'value' => $form_state['values']['organisationstyp']
        ) 
      )
    ),
    'field_organisation_name' => array(
      'und' => array(
        0 => array(
          'value' => $form_state['values']['name']
        ) 
      )
    ),
    'field_organisation_website' => array(
      'und' => array(
        0 => array(
          'value' => $form_state['values']['website']
        )
      )
    )
  );

  user_save($user, $userEdit);

  //db_query('DELETE FROM {users_roles} WHERE uid = :uid', array(':uid' => $user->uid));
  db_query('INSERT INTO {users_roles} (uid, rid) VALUES (:uid, :rid)', array(':uid' => $user->uid, ':rid' => ROLE_ORGANISATION));
  
  // Finished, flush cash
  //cache_clear_all('menu:'. $user->uid, TRUE);

  drupal_set_message(t('Ihr Profil wurde erfolgreich als Organisation aufgewertet. Sie können sich nun zusätzlich als gemeinnützig anerkennen lassen (s.u.).'));

}

// FORM 2

/* User already acts as organization, now become trusted */
function depot_user_organisation_request_form($form, &$form_state) {

  global $user;

  $intro_text = '<hr /><h3>'.t('Als gemeinnützig anerkennen lassen').'</h3>';
  $intro_text .= '<p>'.t('Hier kannst Du die Organisation für die Du handelst als Gemeinwohl-Organisation anerkennen lassen, um günstiger Ressourcen ausleihen zu können. Erläuterungen dazu siehe <a href="/faq">hier</a>. Wenn Deine Organisation keinen Freistellungsbescheid hat, stelle bitte kurz dar, wie Deine Organisation das Gemeinwohl stärkt.').'</p>';

  //form_load_include($form_state, 'inc', 'mymodule', 'plugins/content_types/NAME_OF_YOUR_CONTENT_TYPE/FILE);

  $form['#attributes']['enctype'] = "multipart/form-data";
  
  $form['intro'] = array(
    '#markup' => $intro_text,
    '#weight' => '-99'
  );

  $form['begruendung'] = array(
    '#title' => t('Begründung'),
    '#description' => t('Bitte ausfüllen, insb. wenn Freistellungsbescheid fehlt.'),
    '#type' => 'textarea',
    '#required' => TRUE,
    '#cols' => 60, 
    '#rows' => 5,
  );

  $form['anhang'] = array(
    '#markup' => '<label for="anhang">'. t('Nachweis (Freistellungsbescheid)') .'</label>
    <input type="file" id="anhang" name="anhang" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx">'
  );

  /*
  NOT WORKING ON TEST :(
  $form['anhang'] = array(
    '#type' => 'managed_file',
    '#title' => t('Nachweis (Freistellungsbescheid)'),
    //'#size' => 40,
    '#required' => FALSE,  
    '#description' => t('Die Datei muss kleiner als <strong>3 Mb</strong> sein. Zulässige Dateierweiterungen: <strong>jpg, pdf, doc & docx</strong>.'),
    '#upload_location' => 'public://',
   // '#upload_validators' => array(
    //  'file_validate_extensions' => array('jpg jpeg pdf doc docx'),
      // Pass the maximum file size in bytes
     // 'file_validate_size' => array(MAX_FILE_SIZE*1024*1024),
   // ),
  );*/
  
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Gemeinnützigkeit beantragen'),
    '#attributes' => array(
      'class' => array('button expand')
    ),
    '#submit' => array('depot_user_organisation_request_form_submit'),
  );

  $form['#validate'][] = 'depot_user_organisation_request_form_validate';

  return $form;
 
}

/**
 * Form API validation callback for the booking form.
 */
function depot_user_organisation_request_form_validate(&$form, &$form_state) {  

  /*if (empty($_FILES['anhang']['name'])){
    form_set_error('Hochladen nicht vergessen');
    drupal_set_message('Hochladen nicht vergessen','alert');
   }*/ if (isset($_FILES['anhang']['name']) && !empty($_FILES['anhang']['name'])){
    if ($_FILES['anhang']['size'] > 3145728){
      form_set_error('Die ausgewählte Datei ist zu groß');
      drupal_set_message('Die ausgewählte Datei ist zu groß','alert'); 
    }
  }
}

/**
 * Form API submit callback for FORM 2
 */
function depot_user_organisation_request_form_submit(&$form, &$form_state) {

  global $user;
  global $base_url;

  $anhang_path = null;
  
  if (isset($_FILES['anhang']['name']) && !empty($_FILES['anhang']['name'])){
    if (!$anhang_path = depot_upload_file($_FILES['anhang'])){
      drupal_set_message('Es gab leider Probleme beim Upload. Bitte probieren Sie es erneut oder kontaktieren Sie das Depot.','alert'); 
      return false;      
    }
  }

  /*if (isset($form_state['values']['anhang']) && !empty($form_state['values']['anhang'])) {
    
    $file = file_load($form_state['values']['anhang']);

    $file->status = FILE_STATUS_PERMANENT;

    $file_saved = file_save($file);
    // Record that depot module is using the file. 
    file_usage_add($file_saved, 'depot_user_organisation_request_form', 'anhang', $file_saved->fid); 
    print_r($file_saved);
    exit();
  }*/

  $mail_body = "Lieber Administrator,\r\n";
  $mail_body .= "Ein Nutzer hat um Anerkennung auf Gemeinnützigkeit gebeten.\r\n";
  $mail_body .= "Das Profil ist unter ".$base_url."/user/".$user->uid."/edit zu finden.\r\n";
  $mail_body .= "Als Bewilligungsgrund wurde folgender genannt: '".$form_state['values']['begruendung']."'\r\n";

  if (!empty($anhang_path)){
    $mail_body .= "Es wurde zudem ein Anhang unter ". $anhang_path ." hinterlegt.\r\n";
  }

  $mail_body .= "Um dem Antrag zu zustimmen, gehen Sie bitte in Ihrem Browser auf ". $base_url ."/depot/user_auth_confirm/".$user->uid;

  //$cc = user_load(1)->mail;
  $params = array(
    'body' => $mail_body,
    'subject' => t('Depot Leipzig: Antrag auf Gemeinnützigkeit'),
  );

  drupal_mail('depot','depot_apply_organisation_form',variable_get('site_mail', ''),'de',$params);
  
  // https://api.drupal.org/api/drupal/includes!mail.inc/function/drupal_mail/7.x
  drupal_set_message(t('Vielen Dank, Ihr Antrag wird umgehend von unseren Administratoren geprüft.'));
  
  $form_state['redirect'] = 'ressourcen/';
}