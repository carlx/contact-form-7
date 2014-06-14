<?php

function wpcf7_current_action() {
	if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
		return $_REQUEST['action'];

	if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
		return $_REQUEST['action2'];

	return false;
}

function wpcf7_admin_has_edit_cap() {
	return current_user_can( 'wpcf7_edit_contact_forms' );
}

function wpcf7_add_tag_generator( $name, $title, $elm_id, $callback, $options = array() ) {
	global $wpcf7_tag_generators;

	$name = trim( $name );
	if ( '' == $name )
		return false;

	if ( ! is_array( $wpcf7_tag_generators ) )
		$wpcf7_tag_generators = array();

	$wpcf7_tag_generators[$name] = array(
		'title' => $title,
		'content' => $elm_id,
		'options' => $options );

	if ( is_callable( $callback ) )
		add_action( 'wpcf7_admin_footer', $callback );

	return true;
}

function wpcf7_tag_generators() {
	global $wpcf7_tag_generators;

	$taggenerators = array();

	foreach ( (array) $wpcf7_tag_generators as $name => $tg ) {
		$taggenerators[$name] = array_merge(
			(array) $tg['options'],
			array( 'title' => $tg['title'], 'content' => $tg['content'] ) );
	}

	return $taggenerators;
}

function wpcf7_save_contact_form( $post_id = -1 ) {
	if ( -1 != $post_id ) {
		$contact_form = wpcf7_contact_form( $post_id );
	}

	if ( empty( $contact_form ) ) {
		$contact_form = new WPCF7_ContactForm();
		$contact_form->initial = true;
	}

	if ( isset( $_POST['wpcf7-title'] ) ) {
		$title = trim( $_POST['wpcf7-title'] );

		$contact_form->title = ( '' === $title )
			? __( 'Untitled', 'contact-form-7' ) : $title;
	}

	if ( isset( $_POST['wpcf7-locale'] ) ) {
		$locale = trim( $_POST['wpcf7-locale'] );

		if ( wpcf7_is_valid_locale( $locale ) ) {
			$contact_form->locale = $locale;
		}
	}

	if ( isset( $_POST['wpcf7-form'] ) ) {
		$contact_form->form = trim( $_POST['wpcf7-form'] );
	}

	$mail = $contact_form->mail;

	if ( isset( $_POST['wpcf7-mail-subject'] ) ) {
		$mail['subject'] = trim( $_POST['wpcf7-mail-subject'] );
	}

	if ( isset( $_POST['wpcf7-mail-sender'] ) ) {
		$mail['sender'] = trim( $_POST['wpcf7-mail-sender'] );
	}

	if ( isset( $_POST['wpcf7-mail-body'] ) ) {
		$mail['body'] = trim( $_POST['wpcf7-mail-body'] );
	}

	if ( isset( $_POST['wpcf7-mail-recipient'] ) ) {
		$mail['recipient'] = trim( $_POST['wpcf7-mail-recipient'] );
	}

	if ( isset( $_POST['wpcf7-mail-additional-headers'] ) ) {
		$mail['additional_headers'] = trim( $_POST['wpcf7-mail-additional-headers'] );
	}

	if ( isset( $_POST['wpcf7-mail-attachments'] ) ) {
		$mail['attachments'] = trim( $_POST['wpcf7-mail-attachments'] );
	}

	$mail['use_html'] = ! empty( $_POST['wpcf7-mail-use-html'] );

	$contact_form->mail = $mail;

	$mail_2 = $contact_form->mail_2;

	$mail_2['active'] = ! empty( $_POST['wpcf7-mail-2-active'] );

	if ( isset( $_POST['wpcf7-mail-2-subject'] ) ) {
		$mail_2['subject'] = trim( $_POST['wpcf7-mail-2-subject'] );
	}

	if ( isset( $_POST['wpcf7-mail-2-sender'] ) ) {
		$mail_2['sender'] = trim( $_POST['wpcf7-mail-2-sender'] );
	}

	if ( isset( $_POST['wpcf7-mail-2-body'] ) ) {
		$mail_2['body'] = trim( $_POST['wpcf7-mail-2-body'] );
	}

	if ( isset( $_POST['wpcf7-mail-2-recipient'] ) ) {
		$mail_2['recipient'] = trim( $_POST['wpcf7-mail-2-recipient'] );
	}

	if ( isset( $_POST['wpcf7-mail-2-additional-headers'] ) ) {
		$mail_2['additional_headers'] = trim(
			$_POST['wpcf7-mail-2-additional-headers'] );
	}

	if ( isset( $_POST['wpcf7-mail-2-attachments'] ) ) {
		$mail_2['attachments'] = trim( $_POST['wpcf7-mail-2-attachments'] );
	}

	$mail_2['use_html'] = ! empty( $_POST['wpcf7-mail-2-use-html'] );

	$contact_form->mail_2 = $mail_2;

	$messages = isset( $contact_form->messages )
		? $contact_form->messages : array();

	foreach ( wpcf7_messages() as $key => $arr ) {
		$field_name = 'wpcf7-message-' . strtr( $key, '_', '-' );

		if ( isset( $_POST[$field_name] ) ) {
			$messages[$key] = trim( $_POST[$field_name] );
		}
	}

	$contact_form->messages = $messages;

	if ( isset( $_POST['wpcf7-additional-settings'] ) ) {
		$contact_form->additional_settings = trim(
			$_POST['wpcf7-additional-settings'] );
	}

	do_action( 'wpcf7_save_contact_form', $contact_form );

	return $contact_form->save();
}

?>