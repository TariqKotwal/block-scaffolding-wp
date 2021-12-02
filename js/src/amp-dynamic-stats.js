import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import {
	PanelBody,
	ToggleControl,
} from "@wordpress/components";

registerBlockType( 'amp-gutenberg/amp-dynamic-stats', {
	title: __('AMP Validation Statistics'),
    icon: 'dashboard',
    category: 'widgets',
    description: __('AMP validation statistics and template mode'),
    keywords: [
		__('statistics'),
		__('url')
	],
    attributes: {
        show: {
            type: 'boolean',
            selector: 'show-template-mode'
        }
    },
	edit: ( props ) => {
        const { attributes, setAttributes } = props;
	    const { show } = attributes;
        return <div { ...props }> <Fragment> <InspectorControls> <PanelBody title="Additional Statistics" initialOpen={ false }> <ToggleControl className="show-template-mode" label="Display AMP template mode" onChange={ () => setAttributes({ show: !show }) } /> </PanelBody> </InspectorControls> </Fragment> <ServerSideRender block="amp-gutenberg/amp-dynamic-stats" attributes={ props.attributes } /> </div>;
    }
} );
