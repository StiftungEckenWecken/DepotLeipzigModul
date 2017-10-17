<?php 

/**
 * Generates the booking editing form.
 */
function depot_booking_edit_form($form, &$form_state, $booking) {

  // Add the field related form elements.
  $form_state['bat_booking'] = $booking;
  field_attach_form('bat_booking', $booking, $form, $form_state, isset($booking->language) ? $booking->language : NULL);
  $form['additional_settings'] = array(
    '#type' => 'vertical_tabs',
    '#weight' => 99,
  );

  // Type author information for administrators.
  $form['author'] = array(
    '#type' => 'fieldset',
    '#access' => user_access('bypass bat_booking entities access'),
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

  $form['type'] = array(
    '#type' => 'value',
    '#value' => $booking->type,
  );

  $form['author']['author_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Authored by'),
    '#maxlength' => 60,
    '#autocomplete_path' => 'user/autocomplete',
    '#default_value' => !empty($booking->author_name) ? $booking->author_name : '',
    '#weight' => -1,
    '#description' => t('Leave blank for %anonymous.', array('%anonymous' => variable_get('anonymous', t('Anonymous')))),
  );
  $form['author']['date'] = array(
    '#type' => 'textfield',
    '#title' => t('Authored on'),
    '#maxlength' => 25,
    '#description' => t('Format: %time. The date format is YYYY-MM-DD and %timezone is the time zone offset from UTC. Leave blank to use the time of form submission.', array('%time' => !empty($booking->date) ? date_format(date_create($booking->date), 'Y-m-d H:i:s O') : format_date($booking->created, 'custom', 'Y-m-d H:i:s O'), '%timezone' => !empty($booking->date) ? date_format(date_create($booking->date), 'O') : format_date($booking->created, 'custom', 'O'))),
    '#default_value' => !empty($booking->date) ? $booking->date : '',
  );

  $form['options'] = array(
    '#type' => 'fieldset',
    '#access' => user_access('bypass bat_booking entities access'),
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
  drupal_set_message(t('Buchung gespeichert'));

  $form_state['redirect'] = 'ressourcen';
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
