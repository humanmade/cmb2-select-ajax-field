<?php

namespace CMB2_Multiselect_Ajax;

const FIELD_TYPE_NAME = 'select_ajax';

function bootstrap() {
	add_action( 'cmb2_render_' . FIELD_TYPE_NAME, __NAMESPACE__ . '\\render_cmb2_field', 10, 5 );
	add_action( 'cmb2_after_form', __NAMESPACE__ . '\\enqueue_scripts', 10, 4 );
	add_action( 'cmb2_sanitize_' . FIELD_TYPE_NAME, __NAMESPACE__ . '\\sanitize_value', 10, 5 );
	add_action( 'wp_ajax_cmb2_multiselect_ajax_search', __NAMESPACE__ . '\\on_ajax_get_search_results' );
}

/**
 * Render the CMB2 field
 */
function render_cmb2_field( \CMB2_Field $field, $escaped_value, int $object_id, string $object_type, object $field_type_object ) {
	$values = $escaped_value ?: [];
	$get_text_for_id = $field->args['get_text_for_id'] ?? function () {
		return 'unknown';
	};
	?>
	<select name="<?php echo esc_attr( $field->_name() ) ?>[]" <?php echo ! empty( $field->args['multiple'] ) ? 'multiple' : '' ?> style="width: 300px" class="cmb2-select-ajax-select" data-field="<?php echo esc_attr( $field->id() ) ?>" data-box="<?php echo esc_attr( $field->cmb_id ) ?>">
		<?php foreach ( $values as $value ) : ?>
			<option selected value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $get_text_for_id( $value ) ) ?></option>
		<?php endforeach ?>
	</select>
	<?php
}

function enqueue_scripts() {
	static $rendered;
	// Only ever render this once.
	if ( $rendered ) {
		return;
	}

	$rendered = true;
	wp_enqueue_style( 'cmb2_multiselect_ajax', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', '4.0.13' );
	wp_register_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', [], '4.0.13', true );
	wp_enqueue_script( 'cmb2_multiselect_ajax', plugin_dir_url( __DIR__ ) . 'assets/admin.js', [ 'jquery', 'select2' ] );
	wp_localize_script( 'cmb2_multiselect_ajax', 'cmb2MultiselectAjax', [
		'searchUrl' => wp_nonce_url(
			add_query_arg( 'action', 'cmb2_multiselect_ajax_search', admin_url( 'admin-ajax.php' ) ),
			'cmb2_multiselect_ajax_search'
		),
	] );

}

function on_ajax_get_search_results() {
	$field = sanitize_key( $_GET['field'] );
	$cmb_id = sanitize_key( $_GET['box'] );
	$search = sanitize_text_field( $_GET['q'] ?? '' );
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'cmb2_multiselect_ajax_search' ) ) {
		return;
	}

	$field = cmb2_get_field( $cmb_id, $field );
	if ( empty( $field->args['get_options'] ) ) {
		return;
	}

	$results = $field->args['get_options']( $search );
	wp_send_json( $results );
}

function sanitize_value( $override_value, array $values ) : array {
	return array_filter( array_map( 'absint', $values ) );
}
