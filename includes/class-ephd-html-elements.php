<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Elements of form UI and others
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPHD_HTML_Elements {

	// Form Elements------------------------------------------------------------------------------------------/

	/**
	 * Add Default Fields
	 *
	 * @param array $input_array
	 * @param array $custom_defaults
	 *
	 * @return array
	 */
	public static function add_defaults( array $input_array, array $custom_defaults=array() ) {

		$defaults = array(
			'id'                => '',
			'name'              => 'text',
			'value'             => '',
			'label'             => '',
			'title'             => '',
			'class'             => '',
			'main_label_class'  => '',
			'label_class'       => '',
			'input_class'       => '',
			'input_group_class' => '',
			'radio_class'       => '',
			'action_class'      => '',
			'container_class'   => '',
			'desc'              => '',
			'info'              => '',
			'placeholder'       => '',
			'readonly'          => false,  // will not be submitted
			'required'          => '',
			'autocomplete'      => false,
			'data'              => false,
			'disabled'          => false,
			'max'               => 50,
			'options'           => array(),
			'label_wrapper'     => '',
			'input_wrapper'     => '',
			'icon_color'        => '',
			'return_html'       => false,
			'unique'            => true,
			'text_class'        => '',
			'icon'              => '',
			'list'              => array(),
			'img_list'          => array(),
			'btn_text'          => '',
			'btn_url'           => '',
			'more_info_text'    => '',
			'more_info_url'     => '',
			'tooltip_title'     => '',
			'tooltip_body'      => '',
			'tooltip_args'      => array(),
			'tooltip_external_links'      => array(),
			'is_pro'            => '',
			'is_pro_feature_ad' => '',
			'pro_tooltip_args'  => array(),
            'input_size'        => 'medium',
			'group_data'        => false
		);
		$defaults = array_merge( $defaults, $custom_defaults );
		return array_merge( $defaults, $input_array );
	}

	/**
	 * Renders an HTML Text field
	 *
	 * @param array $args Arguments for the text field
	 * @param bool $return_html
	 * @return false|string
	 */
	public static function text( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$readonly = $args['readonly'] ? ' readonly' : '';
		$required = empty( $args['required'] ) ? '' : ' required';

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$data_escaped = self::get_data_escaped( $args['data'] ); ?>

		<div class="ephd-input-group ephd-admin__text-field <?php echo esc_html( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
			    echo wp_kses_post( $args['label'] );
			    if ( ! empty( $args['tooltip_body'] ) ) {
			        self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
			    }
				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['label'] );
				}
				if ( ! empty( $args['desc'] ) ) {
					echo wp_kses_post( $args['desc'] );
				}   ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">
			    <input type="text"
			           class="ephd-input--<?php echo esc_attr( $args['input_size'] ); ?>"
			           name="<?php echo esc_attr( $args['name'] ); ?>"
			           id="<?php echo  esc_attr( $args['name'] ); ?>"
			           autocomplete="<?php echo ( $args[ 'autocomplete' ] ? 'on' : 'off' ); ?>"
			           value="<?php echo esc_attr( $args['value'] ); ?>"
			           placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"						<?php
			           echo $data_escaped . esc_attr( $readonly . $required );						?>
			           maxlength="<?php echo esc_attr( $args['max'] ); ?>"
			    >
			</div>

		</div>		<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders several HTML input text horizontally
	 *
	 * @param array $args
	 * @param bool $return_html
	 *
	 * @return false|string
	 */
	public static function horizontal_text_inputs( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$defaults = array(
			'inputs' => array()
		);

		$args = self::add_defaults( $args, $defaults );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

        <div class="ephd-input-group ephd-admin__text-field <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

            <label class="<?php echo esc_attr( $args['label_class'] ); ?>">  <?php
		        echo wp_kses_post( $args['label'] );
		        if ( ! empty( $args['tooltip_body'] ) ) {
			        self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
		        }
		        if ( $args['is_pro'] ) {
			        self::display_pro_setting_tag( $args['label'] );
		        }        ?>
            </label>

            <div class="ephd-text-inputs-container">    <?php

				foreach( $args['inputs'] as $input ) {

					$input = self::add_defaults( $input, $defaults );
					$input = self::get_specs_info( $input );  ?>

                    <div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">

                        <input type="text"
                               class="ephd-input--<?php echo esc_attr( $args['input_size'] ); ?>"
                               name="<?php echo  esc_attr( $input['name'] ); ?>"
                               id="<?php echo  esc_attr( $input['name'] ); ?>"
                               autocomplete="<?php echo ( $args[ 'autocomplete' ] ? 'on' : 'off' ); ?>"
                               value="<?php echo esc_attr( $input['value'] ); ?>"
                               placeholder="<?php echo esc_attr( $input['placeholder'] ); ?>"
                               maxlength="<?php echo esc_attr( $input['max'] ); ?>"
                        >

                        <label class="ephd-label" for="<?php echo esc_attr( $input['name'] ); ?>">
                            <span class="ephd-label__text"><?php echo wp_kses_post( $input['label'] ); ?></span>
                        </label>

                    </div>  <?php
				}   ?>
            </div>

        </div>	<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML textarea
	 *
	 * @param array $args Arguments for the text field
	 * @param bool $return_html
	 *
	 * @return false|string
	 */
	public static function textarea( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$defaults = array(
			'name'        => 'textarea',
			'class'       => 'large-text',
			'rows'        => 4,
			'main_tag'    => 'li'
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$html_tag_escaped = $args['main_tag'] == 'div' ? 'div' : 'li';;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<<?php echo esc_attr( $html_tag_escaped ); ?> class="ephd-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

		<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">
			<?php echo wp_kses_post( $args['label'] );

			if ( ! empty( $args['tooltip_body'] ) ) {
				self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
			}
			if ( $args['is_pro'] ) {
				self::display_pro_setting_tag( $args['pro_tooltip_args'] );
			}
			if ( $args['is_pro_feature_ad'] ) {
				self::display_pro_setting_tag_pro_feature_ad( $args['pro_tooltip_args'] );
			}   ?>
		</label>
		<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">
			<textarea
					class="ephd-input--<?php echo esc_attr( $args['input_size'] ); ?>"
					rows="<?php echo esc_attr( $args['rows'] ); ?>"
					name="<?php echo esc_attr( $args['name'] ); ?>"
					id="<?php echo esc_attr( $args['name'] ); ?>"
					value="<?php echo esc_attr( $args['value'] ); ?>"
					placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
				<?php echo ( $args['disabled'] ? ' disabled="disabled"' : '' ); ?> ><?php echo esc_html( $args['value'] )/* do not leave empty space here in HTML via PHP */; ?></textarea> <?php

			if ( $args['desc'] ) {  ?>
				<div class="ephd-input_description"><i><?php echo wp_kses_post( $args['desc'] ); ?></i></div><?php
			}   ?>
		</div>

		</<?php echo esc_attr( $html_tag_escaped ); ?>>		<?php

		if ( ! empty( $args['info'] ) ) { ?>
			<span class="ephd-info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span>		<?php
		}

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML Checkbox
	 *
	 * @param array $args
	 * @param bool $return_html
	 *
	 * @return string
	 */
	public static function checkbox( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$defaults = array(
			'name'         => 'checkbox',
		);
		$args = self::add_defaults( $args, $defaults );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<div class="config-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">				<?php
				echo wp_kses_post( $args['label'] ); ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['container_class'] ); ?>">
				<input type="checkbox"
				       name="<?php echo esc_attr( $args['name'] ); ?>"
				       id="<?php echo esc_attr( $args['name'] ); ?>"
				       value="on"
				       class="<?php echo esc_attr( $args['input_class'] ); ?>"		<?php
						echo checked( "on", $args['value'], false ); ?> >
			</div>
		</div>			<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML Toggle ( checkbox )
	 *
	 * @param array $args
	 * textLoc - left, right
	 * @return false|string
	 */
	public static function checkbox_toggle( $args = array() ) {
		$defaults = array(
			'name'          => '',
			'text'          => '',
			'data'          => '',
			'topDesc'       => '',
			'bottomDesc'    => '',
			'textLoc'       => 'left',
			'checked'       => false,
			'toggleOnText'  => esc_html__( 'on', 'help-dialog' ),
			'toggleOffText' => esc_html__( 'off', 'help-dialog' ),
			'return_html'   => false,
		);
		$args       = self::add_defaults( $args, $defaults );
		$args       = self::get_specs_info( $args );
		$text       = $args['text'];
		$topDesc    = $args['topDesc'];
		$bottomDesc = $args['bottomDesc'];
		$group_data_escaped = self::get_data_escaped( $args['group_data'] );

		if ( $args['return_html'] ) {
			ob_start();
		}   ?>

		<div id="<?php echo esc_attr( $args['id'] ); ?>" class="ephd-settings-control-container ephd-settings-control-type-toggle <?php 
		echo 'ephd-settings-control-type-toggle--' . esc_attr( $args['textLoc'] ); ?> <?php echo esc_attr( $args['input_group_class'] ); ?>" data-field="<?php echo esc_attr( $args['data'] ); ?>" <?php echo $group_data_escaped; ?>>     <?php

			if ( ! empty( $topDesc ) ) {    ?>
				<div class="ephd-settings-control__description"><?php echo wp_kses_post( $topDesc ); ?></div>  <?php
			}   ?>

			<div class="ephd-settings-control__field">
				<label class="ephd-settings-control__title"><?php
					echo esc_html( $text );
					if ( ! empty( $args['desc'] ) ) {
						echo wp_kses_post( $args['desc'] );
					}
					if ( $args['is_pro'] ) {
						self::display_pro_setting_tag( $args['pro_tooltip_args'] );
					}
					if ( $args['is_pro_feature_ad'] ) {
						self::display_pro_setting_tag_pro_feature_ad( $args['pro_tooltip_args'] );
					} ?>
				</label>
				<div class="ephd-settings-control__input <?php echo esc_attr( $args['input_class'] ); ?>">
					<label class="ephd-settings-control-toggle">
						<input type="checkbox" class="ephd-settings-control__input__toggle" value="on" name="<?php echo esc_attr( $args['name'] ); ?>" <?php checked( true, $args['checked'] ); ?>>
						<span class="ephd-settings-control__input__label" data-on="<?php echo esc_attr( $args['toggleOnText'] ); ?>" data-off="<?php echo esc_attr( $args['toggleOffText'] ); ?>"></span>
						<span class="ephd-settings-control__input__handle"></span>
					</label>
				</div>
			</div>			<?php

			if ( ! empty( $bottomDesc ) ) {     ?>
				<div class="ephd-settings-control__description"><?php echo wp_kses_post( $bottomDesc ); ?></div>  <?php
			}   ?>

		</div>		<?php

		if ( $args['return_html'] ) {
			return ob_get_clean();
		}
	}

	/**
	 * Renders an HTML drop-down box
	 *
	 * @param array $args
	 */
	public static function dropdown( $args = array() ) {

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<div class="ephd-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>
			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
				echo wp_kses_post( $args['label'] );
				if ( ! empty( $args['tooltip_body'] ) ) {
					self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}
				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['label'] );
				}                ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">

				<select name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">     <?php
					foreach( $args['options'] as $key => $value ) {
						$label = is_array( $value ) ? $value['label'] : $value;
                        $class = isset( $value['class'] ) ? $value['class'] : '';
						echo '<option value="' . esc_attr( $key ) . '" class="' . esc_attr( $class ) . '"' . selected( $key, $args['value'], false ) . '>' . esc_html( $label ) . '</option>';
					}  ?>
				</select>
			</div>		<?php

			if ( ! empty( $args['info'] ) ) { ?>
				<span class='ephd-info-icon'><p class='hidden'><?php echo esc_html( $args['info'] ); ?></p></span>			<?php
			}	?>

		</div>		<?php
	}

	/**
	 * Renders several HTML radio buttons in a Row
	 * Type of Radio buttons: use the input_group_class
	 *          ephd-radio-vertical-group-container             Regular Radio Horizontal Group
	 *          ephd-radio-vertical-button-group-container      Button Style Radio Horizontal Group
	 *
	 * @param array $args
	 * @return false|string
	 */
	public static function radio_buttons_horizontal( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
            'desc_condition'    => '',
			'options_exclude'	=> [],
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );

		if ( $args['return_html'] ) {
			ob_start();
		}   ?>

        <div class="ephd-input-group ephd-radio-horizontal-button-group-container <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<span class="ephd-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>">                <?php
				echo esc_html( $args['label'] );
                if ( ! empty( $args['tooltip_body'] ) ) {
	                self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
                }

                if ( ! empty( $args['info'] ) ) { ?>
                    <span class="ephd-info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span> <?php
                }
                if ( $args['is_pro'] ) {
	                self::display_pro_setting_tag( $args['label'] );
                } ?>
            </span>

            <div class="ephd-radio-buttons-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php

				foreach( $args['options'] as $key => $label ) {
					if ( in_array( $key, $args['options_exclude'] ) ) {
						continue;
					}	?>
                    <div class="ephd-input-container">

                        <input class="ephd-input" type="radio"
                               name="<?php echo esc_attr( $args['name'] ); ?>"
                               id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
                               value="<?php echo esc_attr( $key ); ?>"  <?php
								checked( $key, $args['value'] );	?>
                        >
	                    <label class="ephd-label" for="<?php echo esc_attr( $args['name'] . $ix ); ?>">
                            <span class="ephd-label__text"><?php echo wp_kses_post( $label ); ?></span>
                        </label>


                    </div> <?php

					$ix++;
				} //foreach				?>

            </div> <?php

			if ( $args['desc'] ) {

                // If there is a condition check for which option is checked.
                $showDesc = '';

                // If there is a condition check for which option is checked.
				if ( isset( $args['desc_condition'] ) ) {
					if ( esc_attr( $args['desc_condition'] ) === esc_attr( $args['value'] ) ) {
						$showDesc = 'radio-buttons-horizontal-desc--show';
					}
				} else {  // If no Condition show desc all the time.
					$showDesc = 'radio-buttons-horizontal-desc--show';
				}
				echo '<span class="radio-buttons-horizontal-desc ' . esc_attr( $showDesc ) . '">' . wp_kses_post( $args['desc'] ) . '</span>';
			}	?>

        </div>	<?php

		if ( $args['return_html'] ) {
			return ob_get_clean();
		}
	}

	/**
	 * Renders several HTML radio buttons in a row but as Icons.
	 *
	 * @param array $args
	 *  options key     = icon CSS name
	 *  option value    = text ( Hidden )*
	 */
	public static function radio_buttons_icon_selection( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<div class="ephd-input-group ephd-admin__radio-icons <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group"  <?php echo $group_data_escaped; ?>>

			<span class="ephd-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo wp_kses_post( $args['label'] );
            if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['label'] );
				} ?>
            </span>

			<div class="ephd-radio-buttons-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php 
			
				foreach( $args['options'] as $key => $label ) {	?>

					<div class="ephd-input-container">
						<label class="ephd-label" for="<?php echo esc_attr( $args['name'] . $ix ); ?>">
							<span class="ephd-label__text"><?php echo esc_html( $label ); ?></span>
							<input class="ephd-input" type="radio"
								name="<?php echo esc_attr( $args['name'] ); ?>"
								id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
								value="<?php echo esc_attr( $key ); ?>" <?php
								checked( $key, $args['value'] );	?>
							>
                            <span class="<?php echo str_contains( $key, 'ep_font_' ) ? '' : 'ephdfa ephdfa-'; ?><?php echo esc_attr( $key ); ?> ephdfa-input-icon"></span>
						</label>
					</div> <?php

					$ix++;
				} //foreach

				if ( ! empty( $args['tooltip_body'] ) ) {
					self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}

				if ( ! empty( $args['info'] ) ) { ?>
					<span class="ephd-info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span> <?php
				} ?>
			</div> <?php

			if ( $args['desc'] ) {
					echo wp_kses_post( $args['desc'] );
			} ?>

		</div>	<?php
	}

	/**
	 * Renders several HTML checkboxes in a row but as Icons.
	 *
	 * @param array $args
	 *  options key     = icon CSS name
	 *  option value    = text ( Hidden )*
	 */
	public static function checkboxes_as_icons_selection( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'checkboxes',
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<div class="ephd-input-group ephd-admin__checkbox-icons <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<span class="ephd-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo wp_kses_post( $args['label'] );
				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['label'] );
				} ?>
            </span>

			<div class="ephd-checkboxes-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php

				foreach( $args['options'] as $key => $label ) {     ?>

					<div class="ephd-input-container">
						<label class="ephd-label" for="<?php echo esc_attr( $args['name'] . $ix ); ?>">
							<span class="ephd-label__text"><?php echo esc_html( $label ); ?></span>
							<input class="ephd-input" type="checkbox"
							       name="<?php echo esc_attr( $args['name'] ); ?>"
							       id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
							       value="<?php echo esc_attr( $key ); ?>"  <?php
									checked( true, in_array( $key, $args['values'] ) ); ?>
							>
							<span class="<?php echo preg_match( '/ep_font_/', $key ) ? '' : 'ephdfa ephdfa-font ephdfa-'; ?><?php echo esc_attr( $key ); ?> ephdfa-input-icon"></span>
						</label>
					</div> <?php

					$ix++;
				} //foreach

				if ( ! empty( $args['tooltip_body'] ) ) {
					self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}

				if ( ! empty( $args['info'] ) ) { ?>
					<span class="ephd-info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span> <?php
				} ?>
			</div> <?php

			if ( $args['desc'] ) {
				echo wp_kses_post( $args['desc'] );
			} ?>

		</div>	<?php
	}

	/**
	 * Renders several HTML radio buttons in a column
	 * Type of Radio buttons: use the input_group_class
	 *          ephd-radio-vertical-group-container           Regular Radio Group
	 *          ephd-radio-vertical-button-group-container    Button Style Radio Group
	 *
	 * @param array $args
	 * @return false|string
	 */
	public static function radio_buttons_vertical( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );

		if ( $args['return_html'] ) {
			ob_start();
		}   ?>
        <div class="ephd-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<span class="ephd-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>">                <?php
				echo wp_kses_post( $args['label'] );
                if ( ! empty( $args['tooltip_body'] ) ) {
	                self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
                }

                if ( ! empty( $args['info'] ) ) { ?>
                    <span class="ephd-info-icon"><p class="hidden"><?php echo esc_html( $args['info'] ); ?></p></span> <?php
                }
                if ( $args['is_pro'] ) {
	                self::display_pro_setting_tag( $args['label'] );
                } ?>
            </span>

            <div class="ephd-radio-buttons-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php

				foreach( $args['options'] as $key => $label ) { ?>
                    <div class="ephd-input-container">

                        <input class="ephd-input" type="radio"
                               name="<?php echo esc_attr( $args['name'] ); ?>"
                               id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
                               value="<?php echo esc_attr( $key ); ?>"  <?php
								checked( $key, $args['value'] );	?>
                        >
                        <label class="ephd-label" for="<?php echo esc_attr( $args['name'] . $ix ); ?>">
                            <span class="ephd-label__text"><?php echo esc_html( $label ); ?></span>
                        </label>


                    </div> <?php

					$ix++;
				} //foreach				?>

            </div> <?php

			if ( $args['desc'] ) {
				echo wp_kses_post( $args['desc'] );
			} ?>

        </div>	<?php

		if ( $args['return_html'] ) {
			return ob_get_clean();
		}
	}

	/**
	 * Single Inputs for text_fields_horizontal function
	 * @param array $args
	 */
	public static function horizontal_text_input( $args = array() ) {

		$args = self::add_defaults( $args );

		$data_escaped = self::get_data_escaped( $args['data'] );		?>

		<div class="<?php echo esc_attr( $args['text_class'] ); ?>">     <?php

			if ( ! empty( $args['label'] ) ) {    ?>
				<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">					<?php
					echo wp_kses_post( $args['label'] ); ?>
				</label>    <?php
			}   ?>

			<div class="input_container">
				<input type="text"
				       name="<?php echo esc_attr( $args['name'] ); ?>"
				       <?php echo empty( $args['id'] ) ? '' : ' id="' . esc_attr( $args['id'] ) . '"'; ?>
				       autocomplete="<?php echo ( $args['autocomplete'] ? 'on' : 'off' ); ?>"
				       value="<?php echo esc_attr( $args['value'] ); ?>"
				       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
				       maxlength="<?php echo esc_attr( $args['max'] ); ?>"					<?php
				echo $data_escaped . ( $args[ 'disabled' ] ? ' disabled="disabled"' : '' );	?> >
			</div>

		</div>	<?php
	}

	/**
	 * Renders several HTML checkboxes in several columns
	 *
	 * @param array $args
	 */
	public static function checkboxes_multi_select( $args = array() ) {

		$defaults = array(
			'id'           => 'checkbox',
			'name'         => 'checkbox',
			'value'        => array(),
			'main_class'   => '',
			'main_tag'     => 'li'
		);
		$args = self::add_defaults( $args, $defaults );
		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$html_tag_escaped = $args['main_tag'] == 'div' ? 'div' : 'li';  ?>

		<<?php echo esc_html( $html_tag_escaped ); ?> class=" <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>    <?php

		if ( $args['label'] != '' ) {   ?>
			<div class="main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo esc_html( $args['label'] ); ?></div>  <?php
		}   ?>

		<div class="ephd-checkboxes-horizontal <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>"> <?php

				foreach( $args['options'] as $key => $label ) {

					$tmp_value = is_array( $args['value'] ) ? $args['value'] : array();
					$checked = in_array( $key, $tmp_value );
					$label = str_replace( ',', '', $label );
					$input_id = $args['name'] . '-' . $ix;  ?>

					<div class="ephd-input-group">
					<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $input_id ); ?>">			<?php
						echo wp_kses_post( $label ); ?>
						</label>
						<div class="input_container <?php echo esc_html( $args['input_class'] ); ?>">
							<input type="checkbox"
							       name="<?php echo esc_attr( $args['name'] ); ?>"
							       id="<?php echo esc_attr( $input_id ); ?>"
							       value="<?php echo esc_attr( $key ); ?>"
								<?php checked( true, $checked ); ?>
						>
						</div>
					</div>   	<?php

					$ix++;
				} //foreach   	?>

		</div>
		</<?php echo esc_html( $html_tag_escaped ); ?>>   <?php
	}

	/**
	 * Output submit button
	 *
	 * @param string $button_label
	 * @param string $action
	 * @param string $main_class
	 * @param string $html - any additional hidden fields
	 * @param bool $unique_button - is this unique button or a group of buttons - use 'ID' for the first and 'class' for the other
	 * @param bool $return_html
	 * @param string $inputClass
	 * @return string
	 */
	public static function submit_button_v2( $button_label, $action, $main_class='', $html='', $unique_button=true, $return_html=false, $inputClass='' ) {

		if ( $return_html ) {
			ob_start();
		}		?>

		<div class="ephd-submit <?php echo esc_attr( $main_class ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">     <?php

			if ( $unique_button ) {  ?>
				<input type="hidden" name="_wpnonce_ephd_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_ephd_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>">
				<input type="submit" id="<?php echo esc_attr( $action ); ?>" class="<?php echo esc_attr( $inputClass ); ?>" value="<?php echo esc_attr( $button_label ); ?>" >  <?php
			} else {    ?>
				<input type="submit" class="<?php echo esc_attr( $action ) . ' ' . esc_attr( $inputClass ); ?>" value="<?php echo esc_attr( $button_label ); ?>" >  <?php
			}

			echo wp_kses_post( $html );  ?>
		</div>  <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML Text field
	 * This has Wrappers because you need to be able to wrap both elements ( Label , Input )
	 *
	 * @param array $args Arguments for the text field
	 * @return string Text field
	 */
	public static function text_basic( $args = array() ) {

		$args = self::add_defaults( $args );
		$id             = $args['name'];
		$autocomplete   = $args['autocomplete'] ? 'on' : 'off';
		$readonly       = $args['readonly'] ? ' readonly' : '';
		$label_wrap_open_escaped  = '';
		$label_wrap_close_escaped = '';
		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$data_escaped = self::get_data_escaped( $args['data'] );

		if ( ! empty( $args['label_wrapper']) ) {
			$label_wrap_open_escaped   = '<' . esc_html( $args['label_wrapper'] ) . ' class="' . esc_attr( $args['main_label_class'] ) . '">';
			$label_wrap_close_escaped  = '</' . esc_html( $args['label_wrapper'] ) . '>';
		}
		if ( ! empty( $args['input_wrapper']) ) {
			$label_wrap_open_escaped   = '<' . esc_html( $args['input_wrapper'] ) . ' class="' . esc_attr( $args['input_group_class'] ) . '" ' . $group_data_escaped . '>';
			$label_wrap_close_escaped  = '<' . esc_html( $args['input_wrapper'] ) . '>';
		}

		if ( ! empty( $args['return_html'] ) ) {
			ob_start();
		}

		echo $label_wrap_open_escaped;  ?>
		<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $args['label'] ); ?></label>		<?php
		echo $label_wrap_close_escaped; ?>

		<input type="text" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $args['input_class'] ); ?>"
		       autocomplete="<?php echo ( $args['autocomplete'] ? 'on' : 'off' ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>"
		       placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" maxlength="<?php echo esc_attr( $args['max'] ); ?>" <?php echo $data_escaped . ( $args['readonly'] ? ' readonly' : '' ); ?> >		<?php

		if ( ! empty( $args['return_html'] ) ) {
			return ob_get_clean();
		}
		return '';
	}

	/**
     * Copy text to clipboard
     *
	 * @param $copy_text
	 * @param $label
	 * @param $return_html
	 *
	 * @return false|string
	 */
	public static function get_copy_to_clipboard_box( $copy_text, $label='', $return_html=true ) {

		if ( ! empty( $return_html ) ) {
			ob_start();
		}
        if ( ! empty( $label ) ) {  ?>
            <span class=""><?php echo esc_html( $label ); ?></span> <?php
		}   ?>
        <span class="ephd-copy-to-clipboard-box-container">
            <span class="ephd-ctc__embed-content">
                <span class="ephd-ctc__embed-notification"><?php echo esc_html__( 'Copied to clipboard', 'help-dialog' ); ?></span>
                <span class="ephd-ctc__embed-code"><?php echo esc_html( $copy_text ); ?></span>
            </span>
            <a class="ephd-ctc__copy-button" href="#">
                <span><?php echo esc_html__( 'Copy', 'help-dialog' ); ?></span>
            </a>
        </span>  <?php
		if ( ! empty( $return_html ) ) {
			return ob_get_clean();
		}

        return '';
	}

	/**
	 * Display a tooltip for admin form fields
	 *
	 * @param $title
	 * @param $body
	 * @param $args
	 */
	public static function display_tooltip( $title, $body, $args = array() ) {
		if ( empty( $body ) ) {
			return;
		}

		$defaults = array(
			'class'         => '',
			'open-icon'     => 'info-circle',
			'open-text'     => '',
			'link_text'     => esc_html__( 'Learn More', 'help-dialog' ),
			'link_url'      => '',
			'link_target'   => '_blank'
		);
		$args = array_merge( $defaults, $args );  ?>

		<div class="ephd__option-tooltip <?php echo esc_attr( $args['class'] ); ?>">
			<span class="ephd__option-tooltip__button <?php echo $args['open-icon'] ? 'ephdfa ephdfa-' . esc_attr( $args['open-icon'] ) : ''; ?>">  <?php
				echo esc_html( $args['open-text'] );  ?>
			</span>
			<div class="ephd__option-tooltip__contents">    <?php
				if ( ! empty( $title ) ) {   ?>
					<div class="ephd__option-tooltip__header">
						<?php echo esc_html( $title );  ?>
					</div>  <?php
				}   ?>
				<div class="ephd__option-tooltip__body">
					<?php echo wp_kses_post( $body ); ?>
				</div>  <?php
				if ( ! empty( $args['link_url'] ) ) {   ?>
					<div class="ephd__option-tooltip__footer">
						<a href="<?php echo esc_url( $args['link_url'] ); ?>" class="ephd__option-tooltip__button" target="<?php echo esc_attr( $args['link_target'] ); ?>">   <?php
							echo esc_html( $args['link_text'] );    ?>
						</a>
					</div>  <?php
				}  ?>
			</div>
		</div>  <?php
	}

	/**
	 *  Display a PRO Feature Ad Popup for settings and a Tool tip if user clicks on the settings.
	 *
	 * @param $args
	 */
	public static function display_pro_setting_tag_pro_feature_ad( $args ) {  ?>

		<div class="ephd__option-pro-tag-container">
			<div class="ephd__option-pro-tag-pro-feature-ad" data-target="<?php echo esc_attr( 'ephd-dialog-pro-feature-ad-pro-setting-tag-' . strtolower( str_replace( ' ', '-', $args['name'] ) ) ); ?>"><?php echo esc_html__( 'PRO', 'help-dialog' ); ?></div> <?php
				EPHD_HTML_Forms::dialog_pro_feature_ad( array(
					'id' => 'ephd-dialog-pro-feature-ad-pro-setting-tag-' . strtolower( str_replace( ' ', '-', $args['name'] ) ),
					'title' => empty( $args['title'] ) ? esc_html__( 'PRO Feature', 'help-dialog' ) : $args['title'],
					'list' => empty( $args['body'] ) ? array() : array($args['body']),
					'btn_text' => empty( $args['btn_text'] ) ? esc_html__('Upgrade Now', 'help-dialog' ) :  $args['btn_text'],
					'btn_url' => empty( $args['btn_url'] ) ? '' : $args['btn_url'],
					'show_close_btn' => 'yes',
					'return_html' => true,
				) ); ?>
		</div> <?php
	}

	/**
	 *  Display a PRO Tag for settings and a Tool tip if user clicks on the settings.
	 *
	 * @param $title
	 */
	public static function display_pro_setting_tag( $title ) {

		if( EPHD_Utilities::is_help_dialog_pro_enabled() ) {
			return;
		}        ?>

		<span class="ephd__option-pro-tag">PRO</span>
		<div class="ephd__option-pro-tooltip">

			<div class="ephd__option-pro-tooltip__contents">
				<div class="ephd__option-pro-tooltip__header"><?php echo esc_html( $title ); ?></div>
				<div class="ephd__option-pro-tooltip__body">
					You need to upgrade to the PRO version to use this feature.
				</div>
				<div class="ephd__option-pro-tooltip__footer">
					<a class="ephd__option-pro-tooltip__button ephd-success-btn" href="https://www.helpdialog.com/help-dialog-pro/" target="_blank" rel="nofollow">Get PRO</a>
				</div>
			</div>
		</div>		 <?php
	}

	/**
	 * Return an HTML Color Picker
	 *
	 * @param array $args Arguments for the text field
	 * @param bool $return_html
	 * @return false|string
	 */
	public static function color( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$data_escaped = self::get_data_escaped( $args['data'] );  ?>

        <div class="ephd-input-group ephd-admin__color-field <?php echo esc_html( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
				echo esc_html( $args['label'] );
				if ( ! empty( $args['tooltip_body'] ) ) {
					self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}
				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['label'] );
				}        ?>
			</label>

			<div class="input_container ekb-color-picker <?php echo esc_attr( $args['input_class'] ); ?>">
				<input type="text"
					   name="<?php echo esc_attr( $args['name'] ); ?>"
					   id="<?php echo esc_attr( $args['name'] ); ?>"
					   value="<?php echo esc_attr( $args['value'] ); ?>"
						<?php echo $data_escaped; ?>
				>
			</div>

		</div>		<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Display standard wp editor tinyMCE
	 *
	 * @param array $args
	 * @param bool $return_html
	 * @return false|string
	 */
	public static function wp_editor( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		wp_enqueue_editor();
		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$data_escaped = self::get_data_escaped( $args['data'] );

		$tinymce_options = [
			'textarea_name' => $args['name'],
			'teeny' => 1
		];

		if ( ! empty( $args['editor_options'] ) ) {
			$tinymce_options = array_merge( $tinymce_options, $args['editor_options'] );
		} ?>

		<div class="ephd-input-group ephd-admin__wp-editor-field <?php echo esc_html( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; ?>>

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
				echo esc_html( $args['label'] );
				if ( ! empty( $args['tooltip_body'] ) ) {
					self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'] );
				}
				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['label'] );
				}        ?>
			</label>

			<div class="input_container ekb-wp-editor <?php echo esc_attr( $args['input_class'] ); ?>" <?php echo $data_escaped; ?>><?php
				wp_editor( $args['value'], $args['name'], $tinymce_options ); ?>
			</div>

		</div>		<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Display settings field as text with a link and PRO tag in front of it
	 *
	 * @param $args
	 */
	public static function display_pro_description_field( $args ) {
		$args = self::add_defaults( $args );
		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>
		<div class="ephd-admin__input-field <?php echo esc_attr( $args['input_group_class'] ); ?>" <?php echo $group_data_escaped; ?>>
			<p>
				<span class="ephd__option-pro-tag"><?php esc_html_e( 'PRO', 'help-dialog' ); ?></span>				<?php
				echo wp_kses_post( $args['desc'] ); ?>
				<a href="<?php echo esc_url( $args['more_info_url'] ); ?>" target="_blank"><?php echo esc_html( $args['more_info_text'] );  ?></a>
			</p>
		</div>  <?php
	}

	/**
	 * Display external links at the bottom of input field
	 *
	 * @param $external_links
	 * @param array $args
	 * @return void
	 */
	public static function display_input_bottom_external_links( $external_links, $args=array() ) {
		$args = wp_parse_args( $args, [
			'css_class' => '',
		] );
		foreach ( $external_links as $one_link ) {
			if ( empty( $one_link['is_bottom_link'] ) ) {
				continue;
			}	?>
			<div class="ephd-input-desc <?php echo esc_attr( $args['css_class'] ); ?>">
				<div class="ephd-input-desc_text">
					<?php echo isset( $one_link['link_desc'] ) ? wp_kses_post( $one_link['link_desc'] ) : ''; ?>
					<a  class="ephd-input-desc__link" target="_blank" href="<?php echo esc_url( $one_link['link_url'] ); ?>"><?php echo esc_html( $one_link['link_text'] ); ?></a><span class="ephdfa ephdfa-external-link"></span>
				</div>
			</div>  <?php
		}
	}

	/**
	 * Return data attributes with escaped keys and values
	 *
	 * @param $data
	 * @return string
	 */
	public static function get_data_escaped( $data ) {
		$data_escaped = '';

		if ( empty( $data ) ) {
			return $data_escaped;
		}

		foreach ( $data as $key => $value ) {
			$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return $data_escaped;
	}

	private static function get_specs_info( $args ) {

		if ( empty( $args['specs'] ) ) {
			return $args;
		}

		$specs_name = $args['specs'];
		$field_specs = EPHD_Config_Specs::get_all_specs();

		if ( empty( $field_specs[$specs_name] ) ) {
			return $args;
		}

		$field_spec = $field_specs[$specs_name];
		$field_spec = wp_parse_args( $field_spec, EPHD_Config_Specs::get_defaults() );

		$args_specs = array(
			'name'              => $field_spec['name'],
			'label'             => empty( $args['label'] ) ? $field_spec['label'] : $args['label'],
			'type'              => $field_spec['type'],
			'input_group_class' => 'ephd-admin__input-field ephd-admin__' . $field_spec['type'] . '-field' . ' ' . $args['input_group_class'],
			'input_class'       => ! empty( $field_spec['is_pro'] ) && ! EPHD_Utilities::is_help_dialog_pro_enabled() ? 'ephd-admin__input-disabled' : '',
			'is_pro'            => empty( $field_spec['is_pro'] ) ? false : $field_spec['is_pro'],
			'desc'              => empty( $args['desc'] ) ? '' : $args['desc'],
			'input_size'        => empty( $field_spec['input_size'] ) ? 'medium' : $field_spec['input_size'],
		);

		if ( $args_specs['type'] == 'select' && empty( $args['options'] ) ) {
			$args['options'] = $field_spec['options'];
		}

		return array_merge( $args, $args_specs );
	}
}