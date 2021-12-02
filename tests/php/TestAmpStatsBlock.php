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
	 * Test init.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::init()
	 */
	public function test_init() {
		$amp_stats_block = new AmpStatsBlocks();

		WP_Mock::expectActionAdded( 'init', [ $amp_stats_block, 'gutenberg_amp_dynamic_stats' ], 10, 1 );

		$amp_stats_block->init();
	}

	/**
	 * Test gutenberg_amp_dynamic_render_callback.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::gutenberg_amp_dynamic_render_callback()
	 */
	public function test_gutenberg_amp_dynamic_render_callback() {

		$amp_stats_block = Mockery::mock( AmpStatsBlocks::class );

		$amp_stats_block->shouldReceive( 'gutenberg_amp_dynamic_render_callback' )
			->once()
			->with( array( 'show' => true ) )
			->andReturn( '<p>There are 4 validated URLs.</p><p>There are 56 validation errors.</p><p>The template mode is standard.</p>' );

		// $amp_stats_block_obj = new AmpStatsBlocks();
		$amp_stats_content = $amp_stats_block->gutenberg_amp_dynamic_render_callback( array( 'show' => true ) );
		$this->assertEquals( '<p>There are 4 validated URLs.</p><p>There are 56 validation errors.</p><p>The template mode is standard.</p>', $amp_stats_content );
	}

	/**
	 * Test register_gutenberg_amp_dynamic_stats_block.
	 *
	 * @covers \XWP\BlockScaffolding\AmpStatsBlocks::register_gutenberg_amp_dynamic_stats_block()
	 */
	public function test_register_gutenberg_amp_dynamic_stats_block() {

		$amp_stats_block = new AmpStatsBlocks();
		$reg_block_args  = array(
			'editor_script' => 'block-scaffolding-js',
			'title'         => __( 'AMP Validation Statistics' ),
			'icon'          => 'dashboard',
			'category'      => 'widgets',
			'description'   => __( 'AMP validation statistics and template mode' ),
			'keywords'      => [ 'statistics', 'url' ],
			'attributes'    => array(
				'show' => array(
					'type' => 'boolean',
				),
			),
		);

		WP_Mock::userFunction( 'register_block_type' )
			->once()
			->with( 'amp-gutenberg/amp-dynamic-stats', $reg_block_args );

		$this->assertEquals( null, $amp_stats_block->register_gutenberg_amp_dynamic_stats_block( 'amp-gutenberg/amp-dynamic-stats', $reg_block_args ) );
	}

}
