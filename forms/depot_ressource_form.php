<?php

/**
 * Form callback wrapper: delete a ressource.
 */
function depot_ressource_delete_form_wrapper($type) {
  // Add the breadcrumb for the form's location.
  bat_type_set_breadcrumb();
  return drupal_get_form('depot_ressource_delete_form', $type);
}

/**
 * Form callback: create or edit a ressource.
 */
function depot_ressource_edit_form($form, &$form_state, $type) {
  
  global $user;

  $form['intro'] = array(
    '#markup' => '<p>'.t('Alle mit <span style="color:red;">*</span> markierten Felder sind auszufüllen. Verfügbarkeiten können im nächsten Schritt verwaltet werden.').'</p>',
    '#weight' => '-99'
  );

  $form['#attributes']['class'][] = 'bat-management-form bat-type-edit-form';

  $form['type'] = array(
    '#type' => 'value',
    '#value' => $type->type,
  );

  // Add the default field elements.
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Name der Ressource'),
    '#default_value' => isset($type->name) ? $type->name : '',
    '#maxlength' => 255,
    '#required' => TRUE,
    '#weight' => -99,
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
  
  // Depot related styling
  foreach ($form as $title => $field){
    if (is_array($field) && strpos($title,'field') >= 0){
      $form[$title]['#attributes']['class'][] = 'column';
    }
  }
  $form['field_anzahl_einheiten']['#attributes']['class'][] = 'medium-6';
  $form['field_anzahl_einheiten']['#weight'] = 6;
  $form['field_minimale_anzahl']['#attributes']['class'][] = 'medium-6';
  $form['field_kategorie']['#attributes']['class'][] = 'medium-6 right';

  $form['images'] = array(
    '#type' => 'container',
    '#weight' => 2,
    '#prefix' => '<fieldset class="medium-12 column"><legend>'.t('Bilder').'</legend>',
    '#suffix' => '</fieldset>'
  );
  $form['field_bild_i']['#prefix'] = '<div class="medium-4 column">';
  $form['field_bild_i']['#suffix'] = '</div>';
  $form['field_bild_ii']['#prefix'] = '<div class="medium-4 column">';
  $form['field_bild_ii']['#suffix'] = '</div>';
  $form['field_bild_ii']['und'][0]['#description'] = '';
  $form['field_bild_iii']['#prefix'] = '<div class="medium-4 column">';
  $form['field_bild_iii']['#suffix'] = '</div>';
  $form['field_bild_iii']['und'][0]['#description'] = '';
  $form['images']['image_i'] = $form['field_bild_i'];
  $form['images']['image_ii'] = $form['field_bild_ii'];
  $form['images']['image_iii'] = $form['field_bild_iii'];
  unset($form['field_bild_i']);
  unset($form['field_bild_ii']);
  unset($form['field_bild_iii']);

  $form['price'] = array(
    '#type' => 'container',
    '#weight' => 4,
    '#prefix' => '<fieldset class="medium-12 column"><legend>'.t('Preis').'</legend>',
    '#suffix' => '</fieldset>'
  );
  $form['price']['price_normal'] = $form['field_kosten'];
  $form['price']['price_discount'] = $form['field_kosten_2'];
  $form['price']['price_deposit'] = $form['field_kaution'];
  $form['price']['price_mwst'] = $form['field_mwst'];
  $form['price']['price_granularity'] = $form['field_abrechnungstakt'];
  unset($form['field_kosten']);
  unset($form['field_kosten_2']);
  unset($form['field_kaution']);
  unset($form['field_abrechnungstakt']);
  unset($form['field_mwst']);
  $form['price']['price_normal']['#prefix'] = '<div class="medium-3 column">';
  $form['price']['price_normal']['#suffix'] = '</div>';
  $form['price']['price_discount']['#prefix'] = '<div class="medium-3 column">';
  $form['price']['price_discount']['#suffix'] = '</div>';
  $form['price']['price_deposit']['#prefix'] = '<div class="medium-2 column">';
  $form['price']['price_deposit']['#suffix'] = '</div>';
  $form['price']['price_mwst']['#prefix'] = '<div class="medium-2 column">';
  $form['price']['price_mwst']['#suffix'] = '</div>';
  $form['price']['price_granularity']['#prefix'] = '<div class="medium-2 column">';
  $form['price']['price_granularity']['#suffix'] = '</div>';

  $form['adress'] = array(
    '#type' => 'container',
    '#weight' => 4,
    '#prefix' => '<fieldset class="medium-12 column"><legend>'.t('Ort der Abholung').'</legend>',
    '#suffix' => '</fieldset>'
  );
  $form['adress']['field_adresse_strasse'] = $form['field_adresse_strasse'];
  $form['adress']['field_adresse_postleitzahl'] = $form['field_adresse_postleitzahl'];
  $form['adress']['field_adresse_ort'] = $form['field_adresse_ort'];
  $form['adress']['field_bezirk'] = $form['field_bezirk'];
  $form['adress']['field__ffnungszeiten'] = $form['field__ffnungszeiten'];
  unset($form['field_adresse_strasse']);
  unset($form['field_adresse_postleitzahl']);
  unset($form['field_adresse_ort']);
  unset($form['field_bezirk']);
  unset($form['field__ffnungszeiten']);
  $form['adress']['field_adresse_strasse']['#prefix'] = '<div class="medium-4 column">';
  $form['adress']['field_adresse_strasse']['#suffix'] = '</div>';
  $form['adress']['field_adresse_postleitzahl']['#prefix'] = '<div class="medium-2 column">';
  $form['adress']['field_adresse_postleitzahl']['#suffix'] = '</div>';
  $form['adress']['field_adresse_ort']['#prefix'] = '<div class="medium-3 column">';
  $form['adress']['field_adresse_ort']['#suffix'] = '</div>';
  $form['adress']['field_bezirk']['#prefix'] = '<div class="medium-3 column">';
  $form['adress']['field_bezirk']['#suffix'] = '</div>';

  $form['uploads'] = array(
    '#type' => 'container',
    '#weight' => 17,
    '#prefix' => '<fieldset class="medium-12 column fieldset-toggle'.(!empty($type->name) || true ? ' toggled' : '').'"><legend>'.t('Links, Anhänge & Textvorlagen').'</legend>',
    '#suffix' => '</fieldset>'
  );
  $form['uploads']['link_i'] = $form['field_links_i'];
  $form['uploads']['link_ii'] = $form['field_links_ii'];
  $form['uploads']['link_iii'] = $form['field_links_iii'];
  $form['uploads']['upload_i'] = $form['field_upload_i'];
  $form['uploads']['upload_ii'] = $form['field_upload_ii'];
  $form['uploads']['upload_i']['#prefix'] = '<div class="medium-6 column">';
  $form['uploads']['upload_i']['#suffix'] = '</div>';
  $form['uploads']['upload_ii']['#prefix'] = '<div class="medium-6 column">';
  $form['uploads']['upload_ii']['#suffix'] = '</div><hr />';
  $form['uploads']['upload_ii']['und'][0]['#description'] = '';
  $form['uploads']['upload_ii']['#weight'] = 20;
  $form['uploads']['buchungsbesaetigung'] = $form['field_text_buchungsbes_tigung'];
  $form['uploads']['verleihvertrag'] = $form['field_verleihvertrag_'];
  $form['uploads']['verleihvertrag_text'] = $form['field_verleihvertrag_text'];
  unset($form['field_upload_i']);
  unset($form['field_upload_ii']);  
  unset($form['field_links_i']);
  unset($form['field_links_ii']);
  unset($form['field_links_iii']);
  unset($form['field_text_buchungsbes_tigung']);
  unset($form['field_verleihvertrag_']);
  unset($form['field_verleihvertrag_text']);

  if (!user_has_role($user->uid,'administrator'))
      $form['field_aktiviert']['#access'] = FALSE;

 # if ($formfield_verleihvertrag_)

  //$form['field_links_i']['und'][0]['#title'] = '<i class="fi fi-link"></i>';
  if (empty($type->field_links_i))
      $form['uploads']['link_ii']['#attributes']['class'][] = 'hide';
  if (empty($type->field_links_ii))
      $form['uploads']['link_iii']['#attributes']['class'][] = 'hide';
  

  // Type author information for administrators.
  $form['author'] = array(
    '#type' => 'fieldset',
    '#access' => FALSE,
   // '#access' => user_access('bypass bat_type entities access'),
    '#title' => t('Authoring information'),
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
  $form['author']['author_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Authored by'),
    '#maxlength' => 60,
    '#autocomplete_path' => 'user/autocomplete',
    '#default_value' => !empty($type->author_name) ? $type->author_name : '',
    '#weight' => -1,
    '#description' => t('Leave blank for %anonymous.', array('%anonymous' => variable_get('anonymous', t('Anonymous')))),
  );
  $form['author']['date'] = array(
    '#type' => 'textfield',
    '#title' => t('Authored on'),
    '#maxlength' => 25,
    '#description' => t('Format: %time. The date format is YYYY-MM-DD and %timezone is the time zone offset from UTC. Leave blank to use the time of form submission.', array('%time' => !empty($type->date) ? date_format(date_create($type->date), 'Y-m-d H:i:s O') : format_date($type->created, 'custom', 'Y-m-d H:i:s O'), '%timezone' => !empty($type->date) ? date_format(date_create($type->date), 'O') : format_date($type->created, 'custom', 'O'))),
    '#default_value' => !empty($type->date) ? $type->date : '',
  );

  $form['revisions'] = array(
    '#type' => 'fieldset',
    '#access' => FALSE,
   // '#access' => user_access('bypass bat_type entities access'),
    '#title' => t('Revision information'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'additional_settings',
    '#attributes' => array(
      'class' => array('type-form-revisions'),
    ),
    '#weight' => 95,
  );

  if (module_exists('revisioning')) {
    $form['revisions']['log'] = array(
      '#type' => 'textarea',
      '#access' => FALSE,
      '#title' => !empty($type->type_id) ? t('Update log message') : t('Creation log message'),
      '#rows' => 4,
      '#description' => t('Provide an explanation of the changes you are making. This will provide a meaningful history of changes to this type.'),
    );

    $options = array();
    if (!empty($type->type_id)) {
      $options[REVISIONING_NO_REVISION] = t('Modify current revision, no moderation');
    }
    $options[REVISIONING_NEW_REVISION_NO_MODERATION] = t('Create new revision, no moderation');
    $options[REVISIONING_NEW_REVISION_WITH_MODERATION] = t('Create new revision and moderate');

    $form['revisions']['revision_operation'] = array(
      '#title' => t('Revision creation and moderation options'),
      '#description' => t('Moderation means that the new revision is not publicly visible until approved by someone with the appropriate permissions.'),
      '#type' => 'radios',
      '#options' => $options,
      '#default_value' => isset($type->type_id) ? REVISIONING_NEW_REVISION_WITH_MODERATION : REVISIONING_NEW_REVISION_NO_MODERATION,
    );

    if (variable_get('revisioning_no_moderation_by_default', FALSE)) {
      $form['revisions']['revision_operation']['#default_value'] = REVISIONING_NEW_REVISION_NO_MODERATION;
    }

    if (!empty($type->type_id)) {
      $revision_count = bat_type_get_number_of_revisions_newer_than($type->revision_id, $type->type_id);

      if ($revision_count == 1) {
        drupal_set_message(t('Please note there is one revision more recent than the one you are about to edit.'), 'warning');
      }
      elseif ($revision_count > 1) {
        drupal_set_message(t('Please note there are @count revisions more recent than the one you are about to edit.',
          array('@count' => $revision_count)), 'warning');
      }
    }
  }
  else {
    if (!empty($type->type_id)) {
      $type_bundle = bat_type_bundle_load($type->type);

      $form['revisions']['revision'] = array(
        '#type' => 'checkbox',
        '#title' => t('Create new revision on update'),
        '#description' => t('If an update log message is entered, a revision will be created even if this is unchecked.'),
        '#default_value' => (isset($type_bundle->data['revision'])) ? $type_bundle->data['revision'] : 0,
      );
    }
    $form['revisions']['log'] = array(
      '#type' => 'textarea',
      '#title' => !empty($type->type_id) ? t('Update log message') : t('Creation log message'),
      '#rows' => 4,
      '#description' => t('Provide an explanation of the changes you are making. This will provide a meaningful history of changes to this type.'),
    );
  }

  // Type publishing options for administrators.
  $form['options'] = array(
    '#type' => 'fieldset',
    '#access' => FALSE,
    //'#access' => user_access('bypass bat_type entities access'),
    '#title' => t('Publishing options'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#group' => 'additional_settings',
    '#attributes' => array(
      'class' => array('type-form-published'),
    ),
    '#weight' => 95,
  );
  $form['options']['status'] = array(
    '#type' => 'checkbox',
    '#title' => t('Published'),
    '#default_value' => $type->status,
  );

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
    '#submit' => $submit + array('depot_ressource_edit_form_submit'),
  );
  $form['actions']['submit']['#attributes']['class'] = array('button');
  
  // If !new form, add more options
  if (!empty($type->name) && bat_type_access('delete', $type)) {

    $form['actions']['availabilities'] = array(
      '#markup' => '<a href="#" title="'.t('Verfügbarkeiten bearbeiten').'" class="button"><fi class="fi fi-calendar"></i> '.t('Verfügbarkeiten bearbeiten').'</a>',
    );

    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Ressource löschen'),
      '#suffix' => l(t('Abbrechen'), 'ressourcen'),
      '#submit' => $submit + array('depot_ressource_form_submit_delete'),
      '#weight' => 45,
    );
  } else {
    $form['actions']['submit']['#attributes']['class'] = array('button expand margin-top-ten');
  }

  $form['#validate'][] = 'depot_ressource_edit_form_validate';

  return $form;
}

/**
 * Form API validate callback for the booking type form.
 */
function depot_ressource_edit_form_validate(&$form, &$form_state) {
  // Notify field widgets to validate their data.
  entity_form_field_validate('bat_type', $form, $form_state);

  // min. Ressourcen < Anzahl Einheiten?
  // URL bei links richtig
}

/**
 * Form API submit callback for the type form.
 */
function depot_ressource_edit_form_submit(&$form, &$form_state) {

  $type = entity_ui_controller('bat_type')->entityFormSubmitBuildEntity($form, $form_state);
  $type->created = !empty($type->date) ? strtotime($type->date) : REQUEST_TIME;

  if ($type->type_id !== '') {
    $type->changed = time();
  }

  if (module_exists('revisioning')) {
    if (isset($type->revision_operation)) {
      $type->revision = ($type->revision_operation > REVISIONING_NO_REVISION);
      if ($type->revision_operation == REVISIONING_NEW_REVISION_WITH_MODERATION) {
        $type->default_revision = FALSE;
      }
    }
  }
  else {
    // Trigger a new revision if the checkbox was enabled or a log message supplied.
    if (!empty($form_state['values']['revision']) ||
        !empty($form['change_history']['revision']['#default_value']) ||
        !empty($form_state['values']['log'])) {
      $type->revision = TRUE;
      $type->log = $form_state['values']['log'];
    }
  }

  if (isset($type->author_name)) {
    if ($account = user_load_by_name($type->author_name)) {
      $type->uid = $account->uid;
    }
    else {
      $type->uid = 0;
    }
  }

  $type->save();

  depot_units_bulk_action('add', $type->name, $type->type_id, $form_state['values']['field_anzahl_einheiten']['und'][0]['value']);

  drupal_set_message(t('Ressource @name gespeichert', array('@name' => $type->name)));

  $form_state['redirect'] = 'ressourcen/'.$type->type_id;
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
  $form_state['redirect'] = array('admin/bat/config/types/manage/' . $form_state['bat_type']->type_id . '/delete', array('query' => $destination));
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