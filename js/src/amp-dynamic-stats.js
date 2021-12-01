import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Fragment } from "@wordpress/element";
import {
	PanelBody,
	ToggleControl,
} from "@wordpress/components";
const { __ } = wp.i18n;

registerBlockType( 'amp-gutenberg/amp-dynamic-stats', {
    apiVersion: 2,
    title: __('AMP Validation Statistics'),
    icon: 'dashboard',
    category: 'widgets',
    description: __('AMP validation statistics and template mode'),
    keywords: ['statistics','url'],
    attributes: {
        show: {
            type: 'boolean',
            selector: 'show-template-mode'
        }
    },
	edit: ( props ) => {
        const blockProps = useBlockProps();
        const { attributes, setAttributes } = props;
	    const { show } = attributes;
        return <div { ...blockProps }> <Fragment> <InspectorControls> <PanelBody title="Additional Statistics" initialOpen={ false }> <ToggleControl className="show-template-mode" label="Display AMP template mode" onChange={ () => setAttributes({ show: !show }) } /> </PanelBody> </InspectorControls> </Fragment> <ServerSideRender block="amp-gutenberg/amp-dynamic-stats" attributes={ props.attributes } /> </div>;
    }
} );
