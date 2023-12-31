<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = WP_Freeio_User::get_user_id();
$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$query_vars = array(
	'post_type'         => 'job_meeting',
	'posts_per_page'    => get_option('posts_per_page'),
	'paged'    			=> $paged,
	'post_status'       => 'publish',
	// 'author' 			=> $user_id
	'meta_query'       => array(
		array(
			'key' => WP_FREEIO_MEETING_PREFIX.'freelancer_id',
			'value'     => $freelancer_id,
			'compare'   => '=',
		),
	)
);

$loop = WP_Freeio_Query::get_posts($query_vars);

?>

<div class="box-dashboard-wrapper">
	<h3 class="widget-title"><?php esc_html_e('Meetings', 'freeio'); ?></h3>
	<div class="inner-list">
        <?php
    	if ( $loop->have_posts() ) {
    		$current_day = strtotime('now');
			while ( $loop->have_posts() ) : $loop->the_post();
				global $post;
				$date = WP_Freeio_Meeting::get_post_meta($post->ID, 'date', true);
				$time = WP_Freeio_Meeting::get_post_meta($post->ID, 'time', true);
				$time_duration = WP_Freeio_Meeting::get_post_meta($post->ID, 'time_duration', true);
				// $freelancer_id = WP_Freeio_Meeting::get_post_meta($post->ID, 'freelancer_id', true);
				$post_id = WP_Freeio_Meeting::get_post_meta($post->ID, 'post_id', true);
				$messages = WP_Freeio_Meeting::get_post_meta($post->ID, 'messages', true);

				if ( get_post_type($post_id) == 'job_applicant') {
					$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
				} elseif( get_post_type($post_id) == 'project_proposal' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id');
				} elseif( get_post_type($post_id) == 'service_order' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id');
				}

				$author_id = $post->post_author;
				$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);

				$datetotime = strtotime($date);
				$week_day = $datetotime > $current_day ? date_i18n('D', $datetotime) : esc_html__('Today', 'freeio');
            	?>
            	<div class="meeting-wrapper d-sm-flex align-items-center">
            		<div class="date text-center">
            			<div class="day"><?php echo date_i18n('d', $datetotime); ?></div>
                        <div class="bottom-date">
                            <span class="week"><?php echo trim($week_day); ?></span> - 
                            <span class="month"><?php echo date_i18n('M', $datetotime); ?></span>
                        </div>
            		</div>
                    <div class="righ-inner d-sm-flex align-items-center">
                		<div class="information">
                			<div class="title-wrapper d-flex align-items-center">
	                			<h3 class="title">
	                				<a href="<?php echo esc_url(get_permalink($job_id)); ?>">
	                					<?php echo get_the_title($job_id); ?>
	                				</a>
	            				</h3>
	            				<?php
	            					$status = WP_Freeio_Meeting::get_post_meta($post->ID, 'status');
	            					if ( $status == 'cancel') {
	            						echo '<span class="label label-danger cancel">'.esc_html__('Canceled', 'freeio').'</span>';
	            					}
	            				?>
            				</div>
                			<div class="meta-bottom">
                				<?php esc_html_e('Meeting with: ', 'freeio'); ?> 
                				<a href="<?php echo esc_url(get_permalink($employer_id)); ?>"><strong><?php echo get_the_title($employer_id); ?></strong></a>
                			</div>
                            <div class="listing-metas d-flex align-items-start flex-wrap">
                                <div class="time"><i class="flaticon-30-days"></i> <?php echo trim($time); ?></div>
                                <div class="time_duration"><i class="flaticon-waiting"></i> <?php echo trim($time_duration); ?> <?php esc_html_e('Minutes', 'freeio'); ?></div>
                            </div>
                		</div>
                		<div class="action-button">

                			<?php
                				$meeting_platform = WP_Freeio_Meeting::get_post_meta($post->ID, 'meeting_platform');
                				if ( $meeting_platform == 'zoom' ) {
                					$zoom_meeting_id = WP_Freeio_Meeting::get_post_meta($post->ID, 'zoom_meeting_id');
    	            				$zoom_meeting_url = WP_Freeio_Meeting::get_post_meta($post->ID, 'zoom_meeting_url');
    	            				?>
    	            				<a href="<?php echo esc_url($zoom_meeting_url); ?>" class="zoom-meeting-btn"><?php esc_html_e('Zoom Meeting', 'freeio'); ?></a>
    	            				<?php
                				}
                				$zoom_meeting_url = '';
                			?>
                			
                			<?php if ( !empty($messages) ) { ?>
                				<div id="meeting-messages-wrapper-<?php echo esc_attr($post->ID); ?>" class="job-apply-email-form-wrapper mfp-hide">
                					<div class="popup-title-wrapper d-flex align-items-center">
                						<h3 class="popup-title"><?php esc_html_e('Meeting History', 'freeio'); ?></h3>
                                        <div class="ms-auto">
                						  <span class="close-popup"><i class="ti-close"></i></span>
                                        </div>
                					</div>
                					<div class="message-meeting-wrapper">
	                					<?php foreach ( $messages as $message ) {
	                						$type = !empty($message['type']) ? $message['type'] : '';
	            						?>
	                						<div class="meesage-meeting">
	                							<div class="heading d-flex align-items-center">
	                								<?php if ( $type == 'create' ) { ?>
	                									<h5><?php echo sprintf(esc_html__('Created by: %s', 'freeio'), get_the_title($employer_id)); ?></h5>
	                								<?php } elseif ( $type == 'reschedule' ) {
	                									$user_post_id = !empty($message['user_post_id']) ? $message['user_post_id'] : 0;
	            									?>
	            										<h5><?php echo sprintf(esc_html__('Re-schedule by: %s', 'freeio'), get_the_title($user_post_id)); ?></h5>
	                								<?php } ?>
	                								<div class="date ms-auto">
	                									<?php echo date_i18n(get_option('date_format'), $message['date']); ?>
	                								</div>
	                							</div>
	                							<div class="content">
	                								<?php echo wpautop($message['message']); ?>
	                							</div>
	                						</div>
	                					<?php } ?>
	                				</div>
                				</div>

                				<a data-toggle="tooltip" href="#meeting-messages-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-messages-job-meeting btn-action-icon messages" data-meeting_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-messages-meeting-nonce' )); ?>" title="<?php echo esc_attr_e('Messages', 'freeio'); ?>"><i class="flaticon-mail"></i> <sup><?php echo count($messages); ?></sup></a>

                			<?php } ?>

                			<a data-toggle="tooltip" href="#job-apply-reschedule-meeting-form-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-reschedule-job-meeting btn-action-icon reschedule" data-meeting_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-reschedule-meeting-nonce' )); ?>" title="<?php echo esc_attr_e('Re-schedule Meeting', 'freeio'); ?>"><i class="flaticon-refresh"></i></a>

                			<?php echo WP_Freeio_Template_Loader::get_template_part('misc/meeting-reschedule-form'); ?>

                			<?php if ( $status != 'cancel') { ?>
	                			<a data-toggle="tooltip" title="<?php esc_attr_e('Cancel Meeting', 'freeio'); ?>" href="javascript:void(0);" class="btn-action-icon btn-cancel-job-meeting cancel" data-meeting_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-cancel-meeting-nonce' )); ?>"><i class="flaticon-delete"></i></a>
	                		<?php } ?>
                		</div>
                    </div>
            	</div>
            	<?php
            endwhile;

			WP_Freeio_Mixes::custom_pagination( array(
				'max_num_pages' => $loop->max_num_pages,
				'prev_text'     => esc_html__( 'Previous page', 'freeio' ),
				'next_text'     => esc_html__( 'Next page', 'freeio' ),
				'wp_query' => $loop
			));

			wp_reset_postdata();
        }  else { ?>
			<div class="not-found"><?php esc_html_e('No meetings found.', 'freeio'); ?></div>
		<?php } ?>
    </div>
	    
</div>