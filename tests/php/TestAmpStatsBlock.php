<?php
/**
 * Tests for AmpStatsBlocks class.
 *
 * @package BlockScaffolding
 */

namespace XWP\BlockScaffolding;

use Mockery;
use WP_Mock;

/**
 * Tests for the AmpStatsBlocks class.
 */
class TestAmpStatsBlock extends TestCase {

	/**
	 * A class instance.
	 *
	 * @var object
	 */
	protected $instance = null;

	/**
	 * Getting instance of the class for rest of the tests.
	 */
	public function setUp():void { // @codingStandardsIgnoreLine
		$this->instance = AmpStatsBlocks::get_instance();
	}

	/**
	 * Test init.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::init()
	 */
	public function test_init() {

		if ( ! defined( 'AMP__VERSION' ) ) {
			define( 'AMP__VERSION', '2.1.4' );
		}
		WP_Mock::expectActionAdded( 'init', [ $this->instance, 'gutenberg_amp_dynamic_stats' ], 10, 1 );

		$this->instance->init();
	}

	/**
	 * Test gutenberg_amp_dynamic_render_callback.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::gutenberg_amp_dynamic_render_callback()
	 */
	public function test_gutenberg_amp_dynamic_render_callback() {

		$validated_urls          = new \stdClass;
		$validated_urls->publish = 2;
		$total_errors            = 0;
		$template_mode           = 'standard';

		\WP_Mock::userFunction( 'wp_count_posts' )
			->with( 'amp_validated_url' )
			->zeroOrMoreTimes()
			->andReturn( $validated_urls );

		\WP_Mock::userFunction( 'wp_count_terms' )
			->with( 'amp_validation_error' )
			->zeroOrMoreTimes()
			->andReturn( $total_errors );

		$amp_options_manager = Mockery::mock( 'alias:\AMP_Options_Manager' );
		$amp_options_manager->shouldReceive( 'get_option' )
			->zeroOrMoreTimes()
			->with( 'theme_support' )
			->andReturn( $template_mode );
		$this->assertEquals( $template_mode, $amp_options_manager->get_option( 'theme_support' ) );

		$amp_stats_blocks = Mockery::mock( AmpStatsBlocks::class );
		$amp_stats_blocks->shouldReceive( 'get_template_mode' )
			->zeroOrMoreTimes()
			->andReturn( $template_mode );
		$this->assertEquals( $template_mode, $amp_stats_blocks->get_template_mode() );

		$amp_stats = $this->instance->get_amp_stats();
		$this->assertEquals( $total_errors, $amp_stats['total_validated_errors'] );
		$this->assertEquals( $validated_urls->publish, $amp_stats['total_validated_urls'] );
		$this->assertEquals( 'standard', $amp_stats['template_mode'] );

		$validated_urls_string = sprintf(
			/* translators: %d total validated URLs */
			__( 'There are %d validated URLs.' ),
			$validated_urls->publish
		);
		$validation_errors_string = sprintf(
			/* translators: %d: total validation errors */
			__( 'There are %d validation errors.' ),
			$total_errors
		);
		$template_mode_string = sprintf(
			/* translators: %s: current template mode */
			__( 'The template mode is %s.' ),
			$template_mode
		);
		$render_template_mode_show = $this->instance->gutenberg_amp_dynamic_render_callback( array( 'show' => true ) );
		$this->assertStringContainsString( $validated_urls_string, $render_template_mode_show );
		$this->assertStringContainsString( $validation_errors_string, $render_template_mode_show );
		$this->assertStringContainsString( $template_mode_string, $render_template_mode_show );

		$render_template_mode_hide = $this->instance->gutenberg_amp_dynamic_render_callback( array( 'show' => false ) );
		$this->assertStringContainsString( $validated_urls_string, $render_template_mode_hide );
		$this->assertStringContainsString( $validation_errors_string, $render_template_mode_hide );
		$this->assertStringNotContainsString( $template_mode_string, $render_template_mode_hide );

		$amp_stats_blocks->shouldReceive( 'amp_stats_get_block_body' )
			->with( $amp_stats, array( 'show' => true ) )
			->zeroOrMoreTimes();
		$amp_block_body = $this->instance->amp_stats_get_block_body( $amp_stats, array( 'show' => true ) );
		$this->assertStringContainsString( $validated_urls_string, $amp_block_body );
		$this->assertStringContainsString( $validation_errors_string, $amp_block_body );
		$this->assertStringContainsString( $template_mode_string, $amp_block_body );

		$amp_stats_blocks->shouldReceive( 'get_total_validated_urls' )
			->zeroOrMoreTimes();
		$this->assertEquals( $validated_urls->publish, $this->instance->get_total_validated_urls() );

		$amp_stats_blocks->shouldReceive( 'get_total_validation_errors' )
			->zeroOrMoreTimes();
		$this->assertEquals( $total_errors, $this->instance->get_total_validation_errors() );
	}

	/**
	 * Test gutenberg_amp_dynamic_stats.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::gutenberg_amp_dynamic_stats()
	 */
	public function test_gutenberg_amp_dynamic_stats() {

		$reg_block_args = array(
			'editor_script'   => 'block-scaffolding-js',
			'render_callback' => array( $this->instance, 'gutenberg_amp_dynamic_render_callback' ),
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

		WP_Mock::userFunction( 'register_block_type' )
			->once()
			->with( 'amp-gutenberg/amp-dynamic-stats', $reg_block_args );

		$this->instance->gutenberg_amp_dynamic_stats();
	}

	/**
	 * Test instance of the class.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::get_instance()
	 */
	public function test_get_instance() {
		$this->assertInstanceOf( AmpStatsBlocks::class, $this->instance->get_instance() );
	}

	public function tearDown() : void  { // @codingStandardsIgnoreLine
		WP_Mock::tearDown();
	}

}
