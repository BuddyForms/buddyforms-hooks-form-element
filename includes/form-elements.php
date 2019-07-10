<?php

/*
 * Add PODS form elementrs in the form elements select box
 */
add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_hooks_elements_to_select', 1, 2 );
function buddyforms_hooks_elements_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}
	$elements_select_options['hooks']['label']                 = 'Add a hook';
	$elements_select_options['hooks']['class']                 = 'bf_show_if_f_type_post';
	$elements_select_options['hooks']['fields']['hooks-field'] = array(
		'label' => __( 'Hook', 'buddyforms' ),
	);

	return $elements_select_options;
}

/*
 * Create the new PODS Form Builder Form Elements
 *
 */
add_filter( 'buddyforms_form_element_add_field', 'buddyforms_hooks_form_builder_form_elements', 1, 5 );
function buddyforms_hooks_form_builder_form_elements( $form_fields, $form_slug, $field_type, $field_id ) {
	global $field_position, $buddyforms;


	switch ( $field_type ) {
		case 'hooks-field':

			unset( $form_fields );

			$hook = '';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['hook'] ) ) {
				$hook = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['hook'];
			}

			$form_fields['general']['hook'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][hook]", array(
				'value'    => str_replace( '-', '_', sanitize_title( $hook ) ),
				'required' => 1
			) );


			$name = 'HOOK ' . $hook;
			$slug = 'hook-' . $field_id;

			$form_fields['general']['name']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", $name );
			$form_fields['general']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", $slug );
			$form_fields['general']['type']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			$form_fields['general']['order'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][order]", $field_position, array( 'id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order' ) );
			break;

	}

	return $form_fields;
}

/*
 * Display the new PODS Fields in the frontend form
 *
 */
add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_hooks_frontend_form_elements', 1, 2 );
function buddyforms_hooks_frontend_form_elements( $form, $form_args ) {
	global $buddyforms, $nonce;

	extract( $form_args );

	$post_type = $buddyforms[ $form_slug ]['post_type'];

	if ( ! $post_type ) {
		return $form;
	}

	if ( ! isset( $customfield['type'] ) ) {
		return $form;
	}

	switch ( $customfield['type'] ) {
		case 'hooks-field':

			ob_start();
			do_action( $customfield['hook'], $form_args );
			$tmp = ob_get_clean();

			$form->addElement( new Element_HTML( $tmp ) );

			break;
	}

	return $form;
}


//add_action( 'some_nice_hook', 'my_some_nice_hook' );
function my_some_nice_hook() {
	echo '<p>something</p>';
}
