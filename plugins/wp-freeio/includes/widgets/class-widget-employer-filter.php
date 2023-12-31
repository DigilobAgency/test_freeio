<?php
/**
 * Widget: Employer Filter
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Widget_Employer_Filter extends WP_Widget {
	/**
	 * Initialize widget
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		parent::__construct(
			'employer_filter_widget',
			__( 'Employer Filter', 'wp-freeio' ),
			array(
				'description' => __( 'Filter for filtering employers.', 'wp-freeio' ),
			)
		);
	}

	/**
	 * Frontend
	 *
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		include WP_Freeio_Template_Loader::locate( 'widgets/employer-filter' );
	}

	/**
	 * Update
	 *
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Backend
	 *
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : '';
		$sort = ! empty( $instance['sort'] ) ? $instance['sort'] : '';
		?>

		<!-- TITLE -->
		<p>
		    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
		        <?php echo __( 'Title', 'wp-freeio' ); ?>
		    </label>

		    <input  class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<!-- BUTTON TEXT -->
		<p>
		    <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>">
		        <?php echo __( 'Button text', 'wp-freeio' ); ?>
		    </label>

		    <input  class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
		</p>

		<h3><?php _e('Filter Fields', 'wp-freeio'); ?></h3>

		<ul class="wp-freeio-filter-fields wp-freeio-filter-employer-fields">
			<?php

			$all_fields = $fields = WP_Freeio_Employer_Filter::get_fields();
			if ( ! empty( $sort ) ) {
				$filtered_keys = array_filter( explode( ',', $sort ) );
				$fields = array_replace( array_flip( $filtered_keys ), $all_fields );
			}
			
			?>

			<input type="hidden" value="<?php echo esc_attr( $sort ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort' ) ); ?>" value="<?php echo esc_attr( $sort ); ?>">

			<?php foreach ( $fields as $key => $value ) :
				if ( !empty($all_fields[$key]) ) {
			?>
					<li data-field-id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $instance[ 'hide_' . $key ] ) ) : ?>class="invisible"<?php endif; ?>>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'hide_' . $key ) ); ?>">
								<?php echo esc_attr( $value['name'] ); ?>
							</label>

							<span class="visibility">
								<input 	type="checkbox" class="checkbox field-visibility" <?php echo ! empty( $instance[ 'hide_'. $key ] ) ? 'checked="checked"' : ''; ?> name="<?php echo esc_attr( $this->get_field_name( 'hide_' . $key ) ); ?>">

								<i class="dashicons dashicons-visibility"></i>
							</span>
						</p>
					</li>
			<?php } ?>
			<?php endforeach ?>
		</ul>

		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.widget .wp-freeio-filter-employer-fields').each(function() {
					var el = $(this);

					el.sortable({
						update: function(event, ui) {
							var data = el.sortable('toArray', {
								attribute: 'data-field-id'
							});

							$('#<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>').attr('value', data).trigger('change');
						}
					});

					$(this).find('input[type=checkbox]').on('change', function() {
						if ($(this).is(':checked')) {
							$(this).closest('li').addClass('invisible');
						} else {
							$(this).closest('li').removeClass('invisible');
						}
					});
				});
			});
		</script>

		<?php
	}
}
register_widget('WP_Freeio_Widget_Employer_Filter');