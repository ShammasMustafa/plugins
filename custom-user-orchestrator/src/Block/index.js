const { registerBlockType } = wp.blocks;
const { useState, useEffect } = wp.element;
const { InspectorControls } = wp.blockEditor || wp.editor;
const { PanelBody, ToggleControl } = wp.components;
const { __ } = wp.i18n;

registerBlockType('cuo/user-table', {
    title: __('User Table', 'custom-user-orchestrator'),
    icon: 'groups',
    category: 'widgets',
    attributes: {
        showEmail: { type: 'boolean', default: true },
        showPhone: { type: 'boolean', default: true },
        showCompany: { type: 'boolean', default: true }
    },
    edit: ( props ) => {
        const { attributes, setAttributes } = props;
        const [ users, setUsers ] = useState( null );
        const [ error, setError ] = useState( null );
        useEffect( () => {
            fetch( '/wp-json/custom-plugin/v1/data' )
                .then( res => res.json() )
                .then( data => setUsers( data ) )
                .catch( e => setError( e.message ) );
        }, [] );

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Columns', 'custom-user-orchestrator')}>
                        <ToggleControl label={__('Email', 'custom-user-orchestrator')}
                                       checked={attributes.showEmail}
                                       onChange={(val) => setAttributes({ showEmail: val })} />
                        <ToggleControl label={__('Phone', 'custom-user-orchestrator')}
                                       checked={attributes.showPhone}
                                       onChange={(val) => setAttributes({ showPhone: val })} />
                        <ToggleControl label={__('Company', 'custom-user-orchestrator')}
                                       checked={attributes.showCompany}
                                       onChange={(val) => setAttributes({ showCompany: val })} />
                    </PanelBody>
                </InspectorControls>

                <div className="cuo-user-table-block">
                    { error && <div className="notice notice-error">{ error }</div> }
                    { !users && !error && <p>{ __('Loading...', 'custom-user-orchestrator') }</p> }
                    { users && Array.isArray(users) && (
                        <table className="widefat">
                            <thead><tr>
                                <th>ID</th>
                                <th>{__('Name','custom-user-orchestrator')}</th>
                                { attributes.showEmail && <th>{__('Email','custom-user-orchestrator')}</th> }
                                { attributes.showPhone && <th>{__('Phone','custom-user-orchestrator')}</th> }
                                { attributes.showCompany && <th>{__('Company','custom-user-orchestrator')}</th> }
                            </tr></thead>
                            <tbody>
                                { users.map( user => (
                                    <tr key={user.id}>
                                        <td>{user.id}</td>
                                        <td>{user.name}</td>
                                        { attributes.showEmail && <td>{user.email}</td> }
                                        { attributes.showPhone && <td>{user.phone}</td> }
                                        { attributes.showCompany && <td>{user.company?.name}</td> }
                                    </tr>
                                ) ) }
                            </tbody>
                        </table>
                    ) }
                </div>
            </>
        );
    },
    save: () => {
        // Rendered on server (or use dynamic block). For simplicity store nothing on save.
        return null;
    }
});
