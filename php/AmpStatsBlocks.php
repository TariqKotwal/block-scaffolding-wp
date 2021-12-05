<?php
/**
 * AmpStatsBlocks class.
 *
 * @package BlockScaffolding
 */

namespace XWP\BlockScaffolding;

/**
 * AMP Statistics Block.
 */
class AmpStatsBlocks {

	/**
	 * A class instance.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * AMP Statistics content.
	 *
	 * @var string
	 */
	protected $amp_body;

	/**
	 * Initialize AMP Statistics Content Body.
	 *
	 * @return void
	 */
	private function __construct() {
		$this->amp_body = '';
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function init() {
		// Check if AMP Plugin is active.
		if ( defined( 'AMP__VERSION' ) ) {
			add_action( 'init', [ $this, 'gutenberg_amp_dynamic_stats' ] );
		}
	}

	/**
	 * Return HTML body of the block.
	 *
	 * @param  array $block_attributes Current block attributes.
	 *
	 * @return string
	 */
	public function gutenberg_amp_dynamic_render_callback( $block_attributes ) {
		ob_start();
		echo $this->amp_stats_get_block_body( $this->get_amp_stats(), $block_attributes );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * To register block on init.
	 *
	 * @return void
	 */
	public function gutenberg_amp_dynamic_stats() {
		$args = array(
			'editor_script'   => 'block-scaffolding-js',
			'render_callback' => array( $this, 'gutenberg_amp_dynamic_render_callback' ),
			'title'           => __( 'AMP Validation Statistics' ),
			'icon'            => 'dashboard',
			'category'        => 'widgets',
			'description'     => __( 'AMP validation statistics and template mode' ),
			'keywords'        => [ 'statistics', 'url' ],
			'attributes'      => array(
				'show' => array(
					'type' => 'boolean',
				),
			),
		);
		register_block_type(
			'amp-gutenberg/amp-dynamic-stats',
			$args
		);
	}

	/**
	 * Return the AMP Statistics.
	 *
	 * @return array
	 */
	public function get_amp_stats() {
		$data = array(
			'total_validated_urls'   => $this->get_total_validated_urls(),
			'total_validated_errors' => $this->get_total_validation_errors(),
			'template_mode'          => $this->get_template_mode(),
		);
		return $data;
	}

	/**
	 * Returns total validated URLs count with 'publish' status.
	 *
	 * @return int
	 */
	public function get_total_validated_urls() {
		$validated_urls = wp_count_posts( 'amp_validated_url' );
		return $validated_urls->publish;
	}

	/**
	 * Returns total validation errors count.
	 *
	 * @return int
	 */
	public function get_total_validation_errors() {
		return wp_count_terms( 'amp_validation_error' );
	}

	/**
	 * Returns currently active template mode.
	 *
	 * @return string
	 */
	public function get_template_mode() {
		return \AMP_Options_Manager::get_option( 'theme_support' );
	}

	/**
	 * Return the block's body containing AMP Statistics.
	 *
	 * @param  array $amp_stats AMP Stats.
	 *
	 * @param  array $block_attributes Current block attributes.
	 *
	 * @return string
	 */
	public function amp_stats_get_block_body( $amp_stats, $block_attributes ) {
		$this->amp_body = '';
		if ( null !== $amp_stats['total_validated_urls'] ) {
			$amp_stats['total_validated_urls'] = intval( $amp_stats['total_validated_urls'] );
			if ( $amp_stats['total_validated_urls'] >= 0 ) {
				if ( 1 === $amp_stats['total_validated_urls'] ) {
					/* translators: %s: total validated urls */
					$this->amp_body .= '<p>' . sprintf( __( 'There is %s validated URL.' ), $amp_stats['total_validated_urls'] ) . '</p>';
				} else {
					/* translators: %s: total validated urls */
					$this->amp_body .= '<p>' . sprintf( __( 'There are %s validated URLs.' ), $amp_stats['total_validated_urls'] ) . '</p>';
				}
			}
		}
		if ( null !== $amp_stats['total_validated_errors'] ) {
			$amp_stats['total_validated_errors'] = intval( $amp_stats['total_validated_errors'] );
			if ( $amp_stats['total_validated_errors'] >= 0 ) {
				if ( 1 === $amp_stats['total_validated_errors'] ) {
					/* translators: %s: total validation errors */
					$this->amp_body .= '<p>' . sprintf( __( 'There is %s validation error.' ), $amp_stats['total_validated_errors'] ) . '</p>';
				} else {
					/* translators: %s: total validation errors */
					$this->amp_body .= '<p>' . sprintf( __( 'There are %s validation errors.' ), $amp_stats['total_validated_errors'] ) . '</p>';
				}
			}
		}
		if ( '' !== $amp_stats['template_mode'] && ! empty( $block_attributes['show'] ) ) {
			/* translators: %s: template mode */
			$this->amp_body .= '<p>' . sprintf( __( 'The template mode is %s' ), $amp_stats['template_mode'] ) . '.</p>';
		}
		if ( '' !== $this->amp_body ) {
			$this->amp_body = '<div' . ( ! empty( $block_attributes['className'] ) ? ' class="' . $block_attributes['className'] . '"' : '' ) . '>' . $this->amp_body . '</div>';
		}
		return $this->amp_body;
	}

	/**
	 * Returns object, creates if not already created. A singleton class function to get the object.
	 *
	 * @return object of the class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
