/* SG Neptun – Editor: Ausrichtung der Team-Karten (Gruppen mit Klasse "sgnl-team") */
(function (wp) {
	if (!wp || !wp.hooks || !wp.compose || !wp.blockEditor) { return; }
	var h = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var Radio = wp.components.RadioControl;

	function classes(cls) { return (cls || '').split(/\s+/).filter(Boolean); }

	var withTeamAlign = wp.compose.createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			if (props.name !== 'core/group') { return h(BlockEdit, props); }
			var list = classes(props.attributes.className);
			if (list.indexOf('sgnl-team') === -1) { return h(BlockEdit, props); }
			var left = list.indexOf('is-team-left') !== -1;
			return h(Fragment, null,
				h(BlockEdit, props),
				h(InspectorControls, null,
					h(PanelBody, { title: 'Team-Karten', initialOpen: true },
						h(Radio, {
							label: 'Ausrichtung',
							selected: left ? 'left' : 'center',
							options: [
								{ label: 'Mittig', value: 'center' },
								{ label: 'Linksbündig (auf einer Linie mit der Überschrift)', value: 'left' }
							],
							onChange: function (v) {
								var next = list.filter(function (c) { return c !== 'is-team-left'; });
								if (v === 'left') { next.push('is-team-left'); }
								props.setAttributes({ className: next.join(' ') });
							}
						}),
						h('p', { style: { color: '#5B6B83', fontSize: '12px', marginTop: '8px' } }, 'Höchstens 3 Karten pro Reihe. Weitere Person: eine Karte duplizieren.')
					)
				)
			);
		};
	}, 'sgnlTeamAlign');

	wp.hooks.addFilter('editor.BlockEdit', 'sgnl-neptun/team-align', withTeamAlign);
})(window.wp);
