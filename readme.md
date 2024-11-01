WordPress Primary Category
====

Select the primary category

https://wordpress.org/plugins/wp-primary-category

- [Installation](#installation)
- [Settings](#settings)
- [Selecting](#selecting)
- [Functions](#functions)
- [Filters](#filters)
- [Shortcode](#shortcode)
- [WP_Query](#wp_query)
- [REST API](#rest-api)
- [To Do](#to-do)

Installation
====
1. Copy the `wp-primary-category` folder into your `wp-content/plugins` folder
2. Activate the `WordPress Primary Category` plugin via the plugin admin page

Settings
====
**How to able the primary category feature?**

1. Open the settings page ( To open that page, we have to two alternatives );
2. Select the taxonomies what you want to able the feature;
3. After selected all taxonomies, click in the save button.

**First Alternative:**
Settings > Primary Category

![first alternative](http://airesgoncalves.com.br/screenshot/wp-primary-category/readme/settings-menu.png)

**Second Alternative:**
Plugins admin page > Settings link in WordPress Primary Category

![second alternative](http://airesgoncalves.com.br/screenshot/wp-primary-category/readme/settings-plugin-page.png)

**Settings Page**

![settings page](http://airesgoncalves.com.br/screenshot/wp-primary-category/readme/settings-page.png)

Selecting
====
After edit your settings, now you can select which will be your primaries categories.

![settings page](http://airesgoncalves.com.br/screenshot/wp-primary-category/readme/select-primary-category.png)

Functions
====
| Name | Argument(s) |
|------|-------------|
| get_primary_categories | mixed ( int, WP_Post, NULL ) **$post**<br>*required*<br>--<br>string **$output**<br>*default value:* OBJECT<br>*others values:* OBJECT, ARRAY_A, ARRAY_N or "ID"<br>*optional* |
| get_primary_category | mixed ( int, WP_Term, object, string ) **$taxonomy**<br>*required*<br>--<br>mixed ( int, WP_Post, NULL ) **$post**<br>*default value:* NULL<br>*optional*<br>--<br>string **$output**<br>*default value:* OBJECT<br>*others values:* OBJECT, ARRAY_A, ARRAY_N or "ID"<br>*optional* |
| the_primary_category | mixed ( int, WP_Term, object, string ) **$taxonomy**<br>*required*<br>--<br>mixed ( int, WP_Post, NULL ) **$post**<br>*default value:* NULL<br>*optional*<br>--<br>string **$output**<br>*default value:* "link"<br>*others values:* "name"<br>*optional*--<br>string **$echo**<br>*default value:* true<br>*optional* |
| is_primary_category | mixed ( int, WP_Term, object ) **$term**<br>*required*<br>--<br>mixed ( string, NULL ) **$taxonomy**<br>*default value:* NULL<br>*optional*<br>--<br>mixed ( int, WP_Post, NULL ) **$post**<br>*default value:* NULL<br>*optional* |
| has_primary_category | mixed ( int, WP_Post, NULL ) **$post**<br>*default value:* NULL<br>*optional* |

**Basic example**

```PHP
$args = array(
	'post_type' => 'book',
);

$query = new WP_Query( $args );
if ( $query->have_posts() ) {
	echo '<ul>';
	while ( $query->have_posts() ) {
		$query->the_post();
		echo '<li>' . get_the_title();
		if ( has_primary_category() ) {
			echo ' - ';
			the_primary_category( 'genre' );
		}
		echo '</li>';
	}
	echo '</ul>';
	wp_reset_postdata();
}
```

Filters
====
| Filter | Argument(s) |
|--------|-------------|
| wp_primary_category_update_option | array **$data**<br>mixed ( array, boolean, NULL ) **$option** |
| wp_primary_category_option | array **$data** |
| wp_primary_category_not_allowed_post_types | array **$post_types** |
| wp_primary_category_post_types_args | array **$args** |
| wp_primary_category_post_types | mixed ( array, boolean ) **$post_types** |
| wp_primary_category_not_allowed_taxonomies | array **$taxonomies** |
| wp_primary_category_taxonomies_args | array **$args** |
| wp_primary_category_taxonomies | mixed ( array, boolean ) **$taxonomies** |
| wp_primary_category_html | string **$html**<br>mixed ( int, WP_Term, object, string ) **$taxonomy**<br>mixed ( int, WP_Post, NULL ) **$post**<br>string **$output** |
| wp_primary_category_rest_api_output | string ( OBJECT, ARRAY_A, ARRAY_N or "ID" ) **$output**<br> array **$request**  |
| wp_primary_category_rest_api_data | array **$data**<br> array **$request** |

**How to use the filters?**

See bellow how to exclude **page post type**.

```PHP
add_filter( 'wp_primary_category_post_types', function( $post_types ) {
	if ( isset( $post_types['page'] ) ) {
		unset( $post_types['page'] );
	}

	return $post_types;
} );
```

or

```PHP
add_filter( 'wp_primary_category_not_allowed_post_types', function( $post_types ) {
	$post_types['page'] = 'page';

	return $post_types;
} );
```

Shortcode
====
| Tag | Attribute(s) |
|-----|--------------|
| the_primary_category | mixed ( int, WP_Term, object, string ) **$taxonomy**<br>*required*<br>--<br>mixed ( int, WP_Post, NULL ) **$post**<br>*default value:* NULL<br>*optional*<br>--<br>string **$output**<br>*default value:* "link"<br>*others values:* "name"<br>*optional* |

#### How to use
```
[wp_primary_category taxonomy="genre"]
```

```
[wp_primary_category taxonomy="genre" post="1"]
```

```
[wp_primary_category taxonomy="genre" post="1" output="name"]
```

WP_Query
====

**See below how get posts with genre primary category**

```PHP
$args = array(
	'post_type' => 'book',
	'meta_query' => array(
		array(
			'key'     => '_wp_primary_category_genre',
			'value'   => array( 2, 4 ), // terms id
			'compare' => 'IN',
		)
	),
);

$query = new WP_Query( $args );
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		// your code here
	}
}
```
https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters

REST API
====

**Endpoint**:
`/wp-json/wp/v2/posts/1`

**Default answer:**
```JSON
{
	"id": 1,
	"title": {
		"rendered": "Hello world!"
	},
	...
	"primary_categories": {
		"category": 1,
		"genre": 2
	},
	...
}
```

**Use the filter below to change the answer:**
```PHP
add_filter( 'wp_primary_category_rest_api_data', function( $data ) {
	if ( isset( $data['genre'] ) ) {
		unset( $data['genre'] );
	}

	return $data;
} );
```

**The new answer:**
```JSON
{
	"id": 1,
	"title": {
		"rendered": "Hello world!"
	},
	...
	"primary_categories": {
		"category": 1
	},
	...
}
```

**Use the filter below to change the output:**
```PHP
add_filter( 'wp_primary_category_rest_api_output', function() {
	return OBJECT; // OBJECT, ARRAY_A, ARRAY_N or 'ID'
} );
```

**The new answer:**
```JSON
{
	"id": 1,
	"title": {
		"rendered": "Hello world!"
	},
	...
	"primary_categories": {
		"category": {
			"term_id": 1,
			"name": "My category",
			"slug": "my-category",
			"term_group": 0,
			"term_taxonomy_id": 2,
			"taxonomy": "category",
			"description": "",
			"parent": 0,
			"count": 1,
			"filter": "raw"
		}
	},
	...
}
```

To Do
====

- add support to Gutenberg ( block editor )
