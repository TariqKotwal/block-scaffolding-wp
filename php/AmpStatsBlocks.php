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
	public function __construct() {
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
		$this->register_gutenberg_amp_dynamic_stats_block( 'amp-gutenberg/amp-dynamic-stats', $args );
	}

	/**
	 * To register custom block.
	 *
	 * @param  string $block_type Block type.
	 *
	 * @param  array  $args Arguments for Block.
	 *
	 * @return mixed boolean|object
	 */
	public function register_gutenberg_amp_dynamic_stats_block( $block_type, $args ) {
		return register_block_type(
			$block_type,
			$args
		);
	}

	/**
	 * Return the AMP Statistics.
	 *
	 * @return array
	 */
	public function get_amp_stats() {
		$amp_options            = \get_option( 'amp-options' );
		$total_validated_errors = \wp_count_terms( 'amp_validation_error' );
		$data                   = array(
			'total_validated_urls'   => \wp_count_posts( 'amp_validated_url' )->publish,
			'total_validated_errors' => ! \is_wp_error( $total_validated_errors ) ? $total_validated_errors : 0,
			'template_mode'          => ! empty( $amp_options['theme_support'] ) ? $amp_options['theme_support'] : '',
		);
		return $data;
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
			/* translators: %s: total validated urls */
			$this->amp_body .= ( $amp_stats['total_validated_urls'] >= 0 ) ? '<p>' . sprintf( _n( 'There is %s validated URL.', 'There are %s validated URLs.', $amp_stats['total_validated_urls'] ), number_format_i18n( $amp_stats['total_validated_urls'] ) ) . '</p>' : '';
		}
		if ( null !== $amp_stats['total_validated_errors'] ) {
			$amp_stats['total_validated_errors'] = intval( $amp_stats['total_validated_errors'] );
			/* translators: %s: total validation errors */
			$this->amp_body .= ( $amp_stats['total_validated_errors'] >= 0 ) ? '<p>' . sprintf( _n( 'There is %s validation error.', 'There are %s validation errors.', $amp_stats['total_validated_errors'] ), number_format_i18n( $amp_stats['total_validated_errors'] ) ) . '</p>' : '';
		}
		if ( '' !== $amp_stats['template_mode'] && ! empty( $block_attributes['show'] ) ) {
			/* translators: %s: template mode */
			$this->amp_body .= '<p>' . sprintf( __( 'The template mode is %s' ), $amp_stats['template_mode'] ) . '.</p>';
		}
		if ( '' !== $this->amp_body ) {
			$this->amp_body = '<div>' . $this->amp_body . '</div>';
		}
		return $this->amp_body;
	}
}
