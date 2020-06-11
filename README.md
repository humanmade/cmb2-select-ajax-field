<table width="100%">
	<tr>
		<td align="left" colspan="2">
			<strong>CMB2 Select Ajax Field</strong><br />
			CMB2 Select2 field with ajax support.
		</td>
	</tr>
	<tr>
		<td>
			A <strong><a href="https://hmn.md/">Human Made</a></strong> project. Maintained by @joehoyle.
		</td>
		<td align="center">
			<img src="https://hmn.md/content/themes/hmnmd/assets/images/hm-logo.svg" width="100" />
		</td>
	</tr>
</table>

# Usage

Register the field type `select_ajax`:

```
$cmb_box->add_field( [
	...
	'type' => 'select_ajax',
	'multiple' => true,
	// get_options is a callback that returns the search results for select2
	'get_options' => function ( string $search ) : array {
		$results = get_results( $search );

		return [
			'pagination' => [
				'more' => false,
			],
			'results' => $results,
		];
	},
	// get_text_for_id is a callback to fetch the name for a stored value. This is needed to populate
	// the initial value of the select2 on page load when an existing value is already set.
	'get_text_for_id' => function ( int $id ) : ?string {
		$post = get_post( $id );
		return $post->post_title;
	},
] );
```
