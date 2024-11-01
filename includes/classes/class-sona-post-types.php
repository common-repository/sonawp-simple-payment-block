<?php
/**
 * SonaWP Custom Post Types Class.
 * @package SonaWP
 */

if ( ! class_exists( 'SonaPostTypes' ) ) {
	class Sona_Post_Types {
		public function __construct() {
			add_Action( 'init', array( $this, 'sona_activate_cpt' ) );
		}
		public function sona_activate_cpt() {
			$labels = array(
				'name'               => _x( 'Products', 'sonawp' ),
				'singular_name'      => _x( 'Product', 'sonawp' ),
				'menu_name'          => _x( 'Products', 'sonawp' ),
				'name_admin_bar'     => _x( 'Product', 'sonawp' ),
				'archives'           => _x( 'Product Archives', 'sonawp' ),
				'attributes'         => _x( 'Product Attributes', 'sonawp' ),
				'parent_item_colon'  => _x( 'Parent Product:', 'sonawp' ),
				'all_items'          => _x( 'Products', 'sonawp' ),
				'add_new_item'       => _x( 'Add New Product', 'sonawp' ),
				'add_new'            => _x( 'Add New', 'sonawp' ),
				'new_item'           => _x( 'New Product', 'sonawp' ),
				'edit_item'          => _x( 'Edit Product', 'sonawp' ),
				'update_item'        => _x( 'Update Product', 'sonawp' ),
				'view_item'          => _x( 'View Product', 'sonawp' ),
				'view_items'         => _x( 'View Products', 'sonawp' ),
				'search_items'       => _x( 'Search Product', 'sonawp' ),
				'not_found'          => _x( 'Not found', 'sonawp' ),
				'not_found_in_trash' => _x( 'Not found in Trash', 'sonawp' ),
			);

			$product_args = array(
				'labels'              => $labels,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'sonawp',
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'rewrite'             => array(
					'slug'       => 'Product',
					'with_front' => false,
				),
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'query_var'           => false,
				'supports'            => array(
					'title',
				),
			);

			$order_labels = array(

				'name'               => '',
				'singular_name'      => _x( 'Order', 'sonawp' ),
				'menu_name'          => _x( 'Order', 'sonawp' ),
				'name_admin_bar'     => _x( 'Order', 'sonawp' ),
				'archives'           => _x( 'Order Archives', 'sonawp' ),
				'attributes'         => _x( 'Order Attributes', 'sonawp' ),
				'parent_item_colon'  => _x( 'Parent Order:', 'sonawp' ),
				'all_items'          => _x( 'Orders', 'sonawp' ),
				'add_new_item'       => _x( 'Add New Order', 'sonawp' ),
				'add_new'            => _x( 'Add New', 'sonawp' ),
				'new_item'           => _x( 'New Order', 'sonawp' ),
				'edit_item'          => _x( 'Edit Order', 'sonawp' ),
				'update_item'        => _x( 'Update Order', 'sonawp' ),
				'view_item'          => _x( 'View Order', 'sonawp' ),
				'view_items'         => _x( 'View Order', 'sonawp' ),
				'search_items'       => _x( 'Search Order', 'sonawp' ),
				'not_found'          => _x( 'Not found', 'sonawp' ),
				'not_found_in_trash' => _x( 'Not found in Trash', 'sonawp' ),
			);

			$order_args = array(
				'labels'              => $order_labels,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'sonawp',
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'rewrite'             => array(
					'slug'       => 'Order',
					'with_front' => false,
				),
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'query_var'           => false,
				'capabilities'        => array(
					'create_posts' => false,
				),
				'map_meta_cap'        => true,
				'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 34"><defs><style>.cls-1{fill:#fff;opacity:0;}.cls-2{fill:#f7f7f7;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><ellipse class="cls-1" cx="18" cy="17" rx="18" ry="17"/><path class="cls-2" d="M28.21,25.18a12.06,12.06,0,0,1-.85.91,13.87,13.87,0,0,1-3.56,2.53,9.68,9.68,0,0,0,.82-1.56,7.15,7.15,0,0,0,.17-5.27c-1-2.5-3.48-3.75-6.13-5.07a18.52,18.52,0,0,1-5.42-3.53A5.47,5.47,0,0,1,11.7,9.31V9.22a1.28,1.28,0,0,1,0-.19,3.71,3.71,0,0,1,.06-.45,5.74,5.74,0,0,1,1.48-2.84c1.54-1.53,4.1-2.25,7.15-1.25a16.27,16.27,0,0,1,9,7.24c-5-1.77-7.81-1-9-1.47a5,5,0,0,1-2.18-1.82c-2.42-3.53-4.3-1.51-3.82.92a7.26,7.26,0,0,0,2.94,4.23,18.93,18.93,0,0,0,6.13,2.56c2.39.68,4.45,1.26,5.41,2.77A5.67,5.67,0,0,1,28.21,25.18Z"/><path class="cls-2" d="M17.36,17.29c-2.66-1.33-5.17-2.58-6.14-5.08a7.15,7.15,0,0,1,.18-5.27,8.24,8.24,0,0,1,.81-1.56A13.92,13.92,0,0,0,8.64,7.91a11.79,11.79,0,0,0-.84.9,5.71,5.71,0,0,0-.6,6.27c1,1.51,3,2.09,5.41,2.77a18.71,18.71,0,0,1,6.13,2.56,7.26,7.26,0,0,1,2.94,4.23c.48,2.43-1.4,4.45-3.82.92h0a5.05,5.05,0,0,0-2.18-1.82c-1.23-.52-4,.3-9-1.47a16.27,16.27,0,0,0,9,7.24h0c3.05,1,5.61.28,7.14-1.25a5.75,5.75,0,0,0,1.49-2.84l.06-.45c0-.07,0-.13,0-.19v-.09a5.48,5.48,0,0,0-1.55-3.88,18,18,0,0,0-5.39-3.52Z"/></g></g></svg>' ),
			);

			register_post_type( 'sonaproduct', $product_args );

			register_post_type( 'sonaorder', $order_args );
		}
	}

	new Sona_Post_Types();

}
