<!-- assets/templates/list_caps.php -->
<div class="wrap">

	<h1 class="nav-tab-wrapper">
		<a href="<?php echo $urls['settings']; ?>" class="nav-tab"><?php esc_html_e( 'Settings', 'civicrm-wp-member-sync' ); ?></a>
		<a href="<?php echo $urls['list']; ?>" class="nav-tab nav-tab-active"><?php esc_html_e( 'Association Rules', 'civicrm-wp-member-sync' ); ?></a>
		<a href="<?php echo $urls['manual_sync']; ?>" class="nav-tab"><?php esc_html_e( 'Manual Synchronize', 'civicrm-wp-member-sync' ); ?></a>
	</h1>

	<p><?php esc_html_e( 'Current Status adds a Membership Capability to the WordPress user, while Expired Status removes the Membership Capability from the WordPress user. This capability will be of the form "civimember_ID", where "ID" is the numeric ID of the Membership Type. So, for Membership Type 2, the capability will be "civimember_2". If you have the "Members" plugin active, then the "restrict_content" capability will also be added.', 'civicrm-wp-member-sync' ); ?></p>

	<p><?php esc_html_e( 'An additional Membership Status Capability will also be added to the WordPress user that is tied to the status of their membership. This capability will be of the form "civimember_ID_NUM", where "ID" is the numeric ID of the Membership Type and "NUM" is the numeric ID of the Membership Status. So, for Membership Type 2 with Membership Status 4, the capability will be "civimember_2_4".', 'civicrm-wp-member-sync' ); ?></p>

	<h3><?php esc_html_e( 'All Association Rules', 'civicrm-wp-member-sync' ); ?><?php

		// If we don't have all our Membership Types populated.
		if ( ! $have_all_types ) {

			// Show the 'Add New' button.
			?> <a class="add-new-h2" href="<?php echo $urls['rules']; ?>"><?php esc_html_e( 'Add New', 'civicrm-wp-member-sync' ); ?></a><?php

		}

	?></h3>

	<?php

	// If we've updated, show message.
	if ( isset( $_GET['syncrule'] ) ) {
		echo '<div id="message" class="updated"><p>';

		// Switch message based on result.
		switch( $_GET['syncrule'] ) {
			case 'edit':
				esc_html_e( 'Association Rule updated.', 'civicrm-wp-member-sync' );
				break;
			case 'add':
				esc_html_e( 'Association Rule added.', 'civicrm-wp-member-sync' );
				break;
			case 'delete':
				esc_html_e( 'Association Rule deleted.', 'civicrm-wp-member-sync' );
				break;
			case 'delete-all':
				esc_html_e( 'Association Rules deleted.', 'civicrm-wp-member-sync' );
				break;
		}

		echo '</p></div>';
	}

	// If we've updated, show message (note that this will only display if we have JS turned off).
	if ( isset( $this->errors ) AND is_array( $this->errors ) ) {

		// Init messages.
		$error_messages = array();

		// Construct array of messages based on error code.
		foreach( $this->errors AS $error_code ) {
			$error_messages[] = $this->error_strings[$error_code];
		}

		// Show them.
		echo '<div id="message" class="error"><p>' . implode( '<br>', $error_messages ) . '</p></div>';

	}

	?>

	<table cellspacing="0" class="wp-list-table widefat fixed users">

		<thead>
			<tr>
				<th class="manage-column column-role" id="civi_member_type_id" scope="col"><?php esc_html_e( 'Civi Membership Type', 'civicrm-wp-member-sync' ); ?></th>
				<th class="manage-column column-role" id="current_rule" scope="col"><?php esc_html_e( 'Current Codes', 'civicrm-wp-member-sync' ); ?></th>
				<?php

				/**
				 * Allow extra columns to be added after "Current Codes".
				 *
				 * @since 0.4
				 */
				do_action( 'civi_wp_member_sync_list_caps_th_after_current' );

				?>
				<th class="manage-column column-role" id="expiry_rule" scope="col"><?php esc_html_e( 'Expired Codes', 'civicrm-wp-member-sync' ); ?></th>
				<?php

				/**
				 * Allow extra columns to be added after "Expired Codes".
				 *
				 * @since 0.4
				 */
				do_action( 'civi_wp_member_sync_list_caps_th_after_expiry' );

				?>
				<th class="manage-column column-role" id="wp_mem_cap" scope="col"><?php esc_html_e( 'Membership Capability', 'civicrm-wp-member-sync' ); ?></th>
				<?php

				/**
				 * Allow extra columns to be added.
				 *
				 * @since 0.3.9
				 */
				do_action( 'civi_wp_member_sync_list_caps_th' );

				?>
			</tr>
		</thead>

		<tbody class="civi_wp_member_sync_table" id="civi_wp_member_sync_list">
			<?php

			// Loop through our data array, keyed by type ID.
			foreach( $data AS $key => $item ) {

				// Construct URLs for this item.
				$edit_url = $urls['rules'] . '&mode=edit&type_id=' . $key;
				$delete_url = wp_nonce_url(
					$urls['list'] . '&syncrule=delete&type_id=' . $key,
					'civi_wp_member_sync_delete_link',
					'civi_wp_member_sync_delete_nonce'
				);

				?>
				<tr>
					<td>
						<?php echo $this->plugin->members->membership_name_get_by_id( $key ); ?><br />
						<div class="row-actions">
							<span class="edit"><a href="<?php echo $edit_url; ?>"><?php esc_html_e( 'Edit', 'civicrm-wp-member-sync' ); ?></a> | </span>
							<span class="delete"><a href="<?php echo $delete_url; ?>" class="submitdelete"><?php esc_html_e( 'Delete', 'civicrm-wp-member-sync' );?></a></span>
						</div>
					</td>
					<td><?php echo $this->plugin->members->status_rules_get_current( $item['current_rule'] ); ?></td>
					<?php

					/**
					 * Allow extra columns to be added after "Current Codes".
					 *
					 * @since 0.4
					 *
					 * @param int $key The current key (type ID).
					 * @param array $item The current item.
					 */
					do_action( 'civi_wp_member_sync_list_caps_td_after_current', $key, $item );

					?>
					<td><?php echo $this->plugin->members->status_rules_get_current( $item['expiry_rule'] );?></td>
					<?php

					/**
					 * Allow extra columns to be added after "Expired Codes".
					 *
					 * @since 0.4
					 *
					 * @param int $key The current key (type ID).
					 * @param array $item The current item.
					 */
					do_action( 'civi_wp_member_sync_list_caps_td_after_expiry', $key, $item );

					?>
					<td><?php

						// Show custom capability for this rule.
						echo CIVI_WP_MEMBER_SYNC_CAP_PREFIX . $key;

						// Is the Members plugin active?
						if ( defined( 'MEMBERS_VERSION' ) ) {

							// Show the custom capability.
							echo '<br>restrict_content';

						}

					?></td>
					<?php

					/**
					 * Allow extra columns to be added.
					 *
					 * @since 0.3.9
					 *
					 * @param int $key The current key (type ID).
					 * @param array $item The current item.
					 */
					do_action( 'civi_wp_member_sync_list_caps_td', $key, $item );

					?>
				</tr>
				<?php

			}

			?>
		</tbody>

	</table>

	<?php if ( ! empty( $data ) ) : ?>

		<form method="post" id="civi_wp_member_sync_rules_form" action="<?php echo $this->admin_form_url_get(); ?>">

			<?php wp_nonce_field( 'civi_wp_member_sync_rule_action', 'civi_wp_member_sync_nonce' ); ?>

			<p class="submit">
				<input class="button button-secondary delete" type="submit" id="civi_wp_member_sync_clear_submit" name="civi_wp_member_sync_clear_submit" value="<?php esc_attr_e( 'Clear Association Rules', 'civicrm-wp-member-sync' ); ?>" /><br />
				<span class="description"><?php esc_html_e( 'Warning: this will delete all your existing Association Rules.', 'civicrm-wp-member-sync' ); ?></span>
			</p>

		</form>

	<?php endif; ?>

</div><!-- /.wrap -->



