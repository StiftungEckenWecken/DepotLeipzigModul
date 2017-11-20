<?php

function depot_user_edit_organisation_form($form, &$form_state) {
  
/*
TODO: Image upload prüfen / fertigstellen
      Felderwerte in $user hinterlegen
      OPT: EMAIL ALS HTML
*/

  global $user;

  $intro_text = '<h3>'.t('Als Organisation auftreten').'</h3>';
  $intro_text .= '<p>'.t('Hier kannst Du die Organisation für die Du handelst als Gemeinwohl-Organisation anerkennen lassen, um günstiger Ressourcen ausleihen zu können. Erläuterungen dazu siehe <a href="/faq">hier</a>. Wenn Deine Organisation keinen Freistellungsbescheid hat, stelle bitte kurz dar, wie Deine Organisation das Gemeinwohl stärkt.').'</p>';
  $is_organisation = user_has_role(ROLE_ORGANISATION);

  if ($is_organisation){
    $intro_text .= '<p>'.t('<strong>Gemeinwohlanerkennung: <span style="color:green;">Aktiv</span></strong></p><hr />').'</p>';
  } else {
    $intro_text .= '<p>'.t('<strong>Gemeinwohlanerkennung: <span style="color:red;">Inaktiv</span></strong></p><hr />').'</p>';
  }

  $form['#attributes']['enctype'] = "multipart/form-data";

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
   // '#default_value' => $category['selected'],
   // '#description' => t('Set this to <em>Yes</em> if you would like this category to be selected by default.'),
  );

  $form['name'] = array(
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#disabled' => $is_organisation
  );

  $form['website'] = array(
    '#title' => t('Website'),
    '#type' => 'textfield',
    '#required' => FALSE,
    '#disabled' => $is_organisation
  );

  $form['begruendung'] = array(
    '#title' => t('Begründung'),
    '#description' => t('Bitte ausfüllen, insb. wenn Freistellungsbescheid fehlt.'),
    '#type' => 'textarea',
    '#required' => FALSE,
    '#cols' => 60, 
    '#rows' => 5,
  );

  $form['anhang'] = array(
    '#type' => 'managed_file',
    '#title' => t('Nachweis (Freistellungsbescheid)'),
    '#size' => 40,
    '#description' => t('Die Datei muss kleiner als <strong>3 MB</strong> sein. Zulässige Dateierweiterungen: <strong>pdf, doc & docx</strong>  .'),
    '#upload_location' => 'public://',
    '#upload_validators' => array(
      'file_validate_extensions' => array('pdf doc docx'),
      // Pass the maximum file size in bytes
     // 'file_validate_size' => array(MAX_FILE_SIZE*1024*1024),
    ),
  );
  
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Beantragen',
    '#attributes' => array(
      'class' => array('button expand')
    ),
    '#submit' => array('depot_user_edit_organisation_form_submit'),
  );

  /*$form['actions'] = array(
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
    '#value' => t('Beantragen'),
    '#submit' => $submit + array('depot_user_edit_organisation_form_submit'),
  );
  $form['actions']['submit']['#attributes']['class'] = array('button');*/
  
  return $form;
}

/**
 * Form API submit callback for the form.
 */
function depot_user_edit_organisation_form_submit(&$form, &$form_state) {
  

  global $user;
  global $base_url;
print_r($form_state);
echo '<br /><br />';

/*
    [values] => Array
        (
            [organisation_ja] => 1
            [organisationstyp] => stiftung
            [name] => Hotten
            [website] => 
            [begruendung] => begrune
            [anhang] => 0
            [submit] => Beantragen
            [form_build_id] => form-jBqJd_WmOmpZQ9gdC0IC2H6P_t3g2t5IsUhrFzUshrU
            [form_token] => he87ac9hsXr0yEFCKseJ7vpRxabxRVQ8HQiU3QcDQ-0
            [form_id] => depot_user_edit_organisation_form
            [op] => Beantragen
        )*/

  if (isset($form_state['values']['anhang']) && !empty($form_state['values']['anhang'])) {
    
    $file = file_load($form_state['values']['anhang']);

    $file->status = FILE_STATUS_PERMANENT;

    $file_saved = file_save($file);
    // Record that depot module is using the file. 
    file_usage_add($file_saved, 'depot_user_edit_organisation_form', 'anhang', $file_saved->fid); 
    print_r($file_saved);
    exit();
  }



  $mail_body = t('Lieber Administrator,\n testtet \n');
  $mail_body .= $base_url.'/users/edit';

  $cc = user_load(1)->mail;
    $params = array(
    'body' => $mail_body,
    'subject' => t('Depot Leipzig: Antrag auf Gemeinnützigkeit'),
    'headers' => array(
    'Cc' => $cc,
    ),
  );

  drupal_mail('depot','depot_apply_organisation_form',$user->mail,'de',$params);
  
  // https://api.drupal.org/api/drupal/includes!mail.inc/function/drupal_mail/7.x

  drupal_set_message(t('Vielen Dank, Ihr Antrag wird demnächst von unseren Administratoren geprüft.'));
  
  $form_state['redirect'] = 'ressourcen/';

}