( function( blocks, element, components, i18n ) {

    const el = element.createElement;

    blocks.registerBlockType( 'cuo/user-table', {
        title: i18n.__( 'User Table', 'cuo' ),
        icon: 'admin-users',
        category: 'widgets',

        edit: () => el(
            'p',
            {},
            'User Table Block (Preview uses REST API data on frontend)'
        ),

        save: () => null // dynamic block
    });

} )( window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n );
