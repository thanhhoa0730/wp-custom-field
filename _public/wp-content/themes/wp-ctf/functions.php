<?php

/**
 *  For debug
 * show template file name
 * ---------------------------------------------------------*/
function show_template() {
    global $template;
    echo basename( $template );
}

// add_action( 'wp_head', 'show_template' );


/**
 * For debug SQL query
 *
 *---------------------------------------------------------*/
function sql_dump( $query ) {
    var_dump( $query );

    return $query;
}

// add_filter( 'query', 'sql_dump' );


/**
 * For debug
 * Show template file name in adminbar.
 * ---------------------------------------------------------*/
function add_debug_menu( $wp_admin_bar ) {
    global $template;

    // $title = sprintf( '<span class="ab-label">%s</span>', '追加メニュー' );

    $wp_admin_bar->add_menu( array(
        'id'    => 'add_dashboard_menu',
        'meta'  => array(),
        'title' => "Template: " . basename( $template )
    ) );

    // $wp_admin_bar->add_menu( array(
    // 	'parent' => 'add_dashboard_menu',
    // 	'id'     => 'add_dashboard_menu-sub',
    // 	'meta'   => array(),
    // 	'title'  => '投稿一覧',
    // 	'href'   => home_url( '/wp-admin/edit.php' )
    // ) );
}

add_action( 'admin_bar_menu', 'add_debug_menu', 9999 );


/**
 *
 *
 * ---------------------------------------------------------*/
const DEVELOPMENT_HOST = array(
    "192.168.33.10",
    "localhost",
    "xxx.google.com",
);


/**
 *
 *
 * ---------------------------------------------------------*/
function isDevelopment() {
    for ( $i = 0; $i <= count( DEVELOPMENT_HOST ); $i ++ ) {
        if ( strpos( $_SERVER["HTTP_HOST"], DEVELOPMENT_HOST[ $i ] ) !== false ) {
            return true;
        }
    }

    return false;
}


/**
 *
 *
 * ---------------------------------------------------------*/
function send_mail_smtp( $phpmailer ) {
    if ( isDevelopment() ) {
        $phpmailer->isSMTP();
        $phpmailer->Host       = "smtp.googlemail.com";
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = 465;
        $phpmailer->Username   = "xxx@gmail.com";
        $phpmailer->Password   = "xxx";
        $phpmailer->SMTPSecure = "ssl";
        // $phpmailer->From       = "hoge@example.com";
        // $phpmailer->SMTPDebug = 2;
    }
}

add_action( "phpmailer_init", "send_mail_smtp" );


/*
---------------------------------------------------------*/
function load_css_js() {
    $dir = get_template_directory_uri();
    $js  = $dir . '/js';
    $css = $dir . '/css';
    wp_enqueue_style( 'style', $css . '/style.min.css', array(), false );
    wp_enqueue_script( 'main', $js . '/main.js', array(), false, true );
}

add_action( 'wp_enqueue_scripts', 'load_css_js' );

/*
---------------------------------------------------------*/
function update_version_query_string( $url ) {
    $version = 1;

    $url = remove_query_arg( 'ver', $url );
    $url = add_query_arg( 'ver', $version, $url );

    return esc_url( $url );
}

add_filter( 'style_loader_src', 'update_version_query_string', 1 );
add_filter( 'script_loader_src', 'update_version_query_string', 1 );

/* CSS for visual editor
---------------------------------------------------------*/
function gutenberg_setup() {
    add_theme_support( 'editor-styles' );
    add_editor_style( '/css/style.min.css' );
}

// add_action( 'after_setup_theme', 'gutenberg_setup' );

/*
 * Disable theme update notification
---------------------------------------------------------*/
// remove_action( 'load-update-core.php', 'wp_update_themes' );
// add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );

/*
---------------------------------------------------------*/
function remove_unuse() {
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
}

add_action( 'init', 'remove_unuse' );


/*
---------------------------------------------------------*/
function title_tag_switcher( $bool = false, $str = '', $trueStartTag = '', $trueEndTag = '', $falseStartTag = '', $falseEndTag = '' ) {
    if ( $bool ) {
        return $trueStartTag . $str . $trueEndTag;
    } else {
        return $falseStartTag . $str . $falseEndTag;
    }
}

/*
---------------------------------------------------------*/
function img_tag_switcher( $bool = false, $trueImg = '', $falseImg = '' ) {
    if ( $bool ) {
        return $trueImg;
    } else {
        return $falseImg;
    }
}


/*
 *
---------------------------------------------------------*/
function change_post_menu_label() {
    global $menu;
    global $submenu;

    $name = 'お知らせ';

    $menu[5][0]                 = $name;
    $submenu['edit.php'][5][0]  = $name . '一覧';
    $submenu['edit.php'][10][0] = "新しい{$name}";
    // $submenu['edit.php'][16][0] = 'タグ';
}

// add_action( 'admin_menu', 'change_post_menu_label' );


/*
 *
---------------------------------------------------------*/
function change_post_object_label() {
    global $wp_post_types;

    $name = 'お知らせ';

    $labels                = $wp_post_types['post']->labels;
    $labels->name          = $name;
    $labels->singular_name = $name;
    $labels->add_new       = _x( '追加', $name );
    $labels->add_new_item  = $name . 'の新規追加';
    $labels->edit_item     = $name . 'の編集';
    $labels->new_item      = "新規" . $name;
    $labels->view_item     = $name . 'を表示';
    $labels->search_items  = $name . 'を検索';
    // $labels->not_found          = '記事が見つかりませんでした';
    // $labels->not_found_in_trash = 'ゴミ箱に記事は見つかりませんでした';
}

// add_action( 'init', 'change_post_object_label' );

/*
*
---------------------------------------------------------*/
function remove_post_support() {    remove_post_type_support( 'post', 'title' );
    remove_post_type_support( 'post', 'editor' );
    remove_post_type_support( 'post', 'author' );
    remove_post_type_support( 'post', 'thumbnail' ); // feature image
    remove_post_type_support( 'post', 'excerpt' );
    remove_post_type_support( 'post', 'trackbacks' );
    remove_post_type_support( 'post', 'custom-fields' );
    remove_post_type_support( 'post', 'tag' );
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'post', 'revisions' );
    remove_post_type_support( 'post', 'page-attributes' );
    remove_post_type_support( 'post', 'post-formats' );

    unregister_taxonomy_for_object_type( 'category', 'post' );
    unregister_taxonomy_for_object_type( 'post_tag', 'post' );
}

// add_action('init','remove_post_support');


/*
* Ref: https://codex.wordpress.org/Conditional_Tags
---------------------------------------------------------*/
function change_main_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Posts page
    if ( $query->is_home() ) {
        $query->set( 'posts_per_page', 5 );
    }

    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        $query->set('posts_per_page', -1);
    }

    // if ($query->is_post_type_archive('xxx')) {
    //     $query->set('posts_per_page', 15);
    // }

    // $query->is_singular( 'xxx' )
    // $query->is_tax( 'solution-category' ) // taxonomy
}

add_action('pre_get_posts', 'change_main_query');

/*
---------------------------------------------------------*/
function tinymce_config( $init ) {
    $init['forced_root_block'] = '<br>';

    return $init;
}

// add_filter('tiny_mce_before_init', 'tinymce_config');

/*
---------------------------------------------------------*/
// WordPress 4.4 over
function change_document_title_parts( $title_parts ) {
    $separator   = '';
    $description = get_bloginfo( 'description' );

    // if (is_home() || is_front_page()) {
    //     $title_parts['title'] = '';
    // }

    $title_parts['tagline'] = '';

    if ( $description ) {
        $separator = ' - ';
    }

    $title_parts['site'] = get_bloginfo( 'name' ) . $separator . $description;

    return $title_parts;
}

// add_filter( 'document_title_parts', 'change_document_title_parts' );

/* Hide items on header Admin bar
---------------------------------------------------------*/
function remove_bar_menus( $wp_admin_bar ) {
    // $wp_admin_bar->remove_menu('wp-logo');      // ロゴ
    // $wp_admin_bar->remove_menu('site-name');    // サイト名
    // $wp_admin_bar->remove_menu('view-site');    // サイト名 -> サイトを表示
    // $wp_admin_bar->remove_menu('dashboard');    // サイト名 -> ダッシュボード (公開側)
    // $wp_admin_bar->remove_menu('themes');       // サイト名 -> テーマ (公開側)
    // $wp_admin_bar->remove_menu('customize');    // サイト名 -> カスタマイズ (公開側)
    // $wp_admin_bar->remove_menu('comments');     // コメント
    // $wp_admin_bar->remove_menu('updates');      // 更新
    // $wp_admin_bar->remove_menu('view');         // 投稿を表示
    // $wp_admin_bar->remove_menu('new-content');  // 新規
    // $wp_admin_bar->remove_menu('new-post');     // 新規 -> 投稿
    // $wp_admin_bar->remove_menu('new-media');    // 新規 -> メディア
    // $wp_admin_bar->remove_menu('new-link');     // 新規 -> リンク
    // $wp_admin_bar->remove_menu('new-page');     // 新規 -> 固定ページ
    // $wp_admin_bar->remove_menu('new-user');     // 新規 -> ユーザー
    // $wp_admin_bar->remove_menu('my-account');   // マイアカウント
    // $wp_admin_bar->remove_menu('user-info');    // マイアカウント -> プロフィール
    // $wp_admin_bar->remove_menu('edit-profile'); // マイアカウント -> プロフィール編集
    // $wp_admin_bar->remove_menu('logout');       // マイアカウント -> ログアウト
    // $wp_admin_bar->remove_menu('search');       // 検索 (公開側)
}

// add_action('admin_bar_menu', 'remove_bar_menus', 201);



/**
 * Hide items on Admin left sidebar
 *
 * ---------------------------------------------------------*/
function remove_menus() {
    remove_menu_page( 'index.php' ); // ダッシュボード
    remove_menu_page( 'edit.php' ); // 投稿
    // remove_menu_page( 'edit.php?post_type=page' ); // 固定
    remove_menu_page( 'post-new.php?post_type=page' );
    remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' ); // 固定 / 新規追加.

    remove_menu_page( 'themes.php' );
    remove_menu_page( 'users.php' );
    remove_menu_page( 'upload.php' ); // メディア
    remove_menu_page( 'edit-comments.php' ); // コメント
    remove_menu_page( 'profile.php' ); // プロフィール
    remove_menu_page( 'tools.php' ); // ツール
    remove_menu_page( 'plugins.php' );
    remove_menu_page( 'options-general.php' );
    remove_menu_page( 'edit.php?post_type=acf-field-group' );
    remove_menu_page( 'cptui_main_menu' );
}

// add_action('admin_menu', 'remove_menus');



/**
 *
 *
 * ---------------------------------------------------------*/
function remove_admin_bar_menu( $wp_admin_bar ) {
    $wp_admin_bar->remove_menu( 'comments' );
    $wp_admin_bar->remove_menu( 'new-content' );
    $wp_admin_bar->remove_menu( 'view' );
}

// add_action( 'admin_bar_menu', 'remove_admin_bar_menu', 100 );



/**
 * Custom menu order(left sidebar)
 *
 * ---------------------------------------------------------*/
function my_custom_menu_order( $menu_order ) {
    if ( ! $menu_order ) {
        return true;
    }

    return array(
        'index.php', //ダッシュボード
        'separator1', //セパレータ１
        'edit.php?post_type=case',
        'edit.php?post_type=whitepaper',
        'edit.php?post_type=column',
        'edit.php?post_type=seminar',
        'edit.php', // post
        // 'separator2', //セパレータ２
        // 'edit.php?post_type=page', //固定ページ
        // 'edit-comments.php', //コメント
        // 'separator-last', //最後のセパレータ
        // 'themes.php', //外観
        // 'plugins.php', //プラグイン
        // 'users.php', //ユーザー
        // 'tools.php', //ツール
        // 'options-general.php', //設定
        // 'upload.php', //メディア (一番下に移動しました)
    );
}

add_filter( 'custom_menu_order', 'my_custom_menu_order' );
add_filter( 'menu_order', 'my_custom_menu_order' );



/*
 * Disable auto html format by TinyMCE
---------------------------------------------------------*/
add_filter( 'tiny_mce_before_init', function ( $init ) {
    $init['wpautop']                 = false;
    $init['apply_source_formatting'] = true;

    return $init;
} );

/*
---------------------------------------------------------*/
// remove_filter( 'the_content', 'wpautop' );

/*
 *
---------------------------------------------------------*/
function my_excerpt_length( $length ) {
    return 80;
}

// add_filter( 'excerpt_length', 'my_excerpt_length' );

/*
 *
---------------------------------------------------------*/
function getLabelClass( $catName ) {
    switch ( $catName ) {
        case 'RECRUIT':
            return 'label-cyan';
            break;
        case 'MEDIA':
            return 'label-green';
            break;
        default:
            return 'label-orange';
    }
}


/*
 *
---------------------------------------------------------*/
function getCategoryName() {
    // Category
    $cat = get_the_category();
    // $catId = $cat[0]->cat_ID;
    $catName = $cat[0]->name;
    // $catSlug = $cat[0]->category_nicename;
    // $link = get_category_link($catId);

    // Custom post category(taxonomy)
    // $category = get_the_terms( $post->ID, 'product-category' );
    // $catName = $category[0]->name;

    return $catName;
}


/*
---------------------------------------------------------*/
function setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

    if ( function_exists( 'add_image_size' ) ) {
        add_image_size('thumb-140x105', 140, 105, true);
        add_image_size('thumb-364x230', 364, 230, true);
        add_image_size('thumb-558x400', 558, 400, true);
    }
}

add_action( 'after_setup_theme', 'setup' );

/*
---------------------------------------------------------*/
function remove_type_attr( $tag ) {
    return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}

add_filter( 'script_loader_tag', 'remove_type_attr' );
add_filter( 'style_loader_tag', 'remove_type_attr' );


/*
 * https://qiita.com/gatespace/items/199c9995e47d668e0fb0
 * https://developer.wordpress.org/reference/hooks/manage_post_type_posts_columns/
 * apply_filters( "manage_{$post_type}_posts_columns", string[] $post_columns )
 * add any column on "admin posts list page"
---------------------------------------------------------*/
function add_columns( $columns ) {
    $new_columns = array();

    foreach ( $columns as $column_name => $column_display_name ) {
        if ( $column_name == 'date' ) {
            $new_columns['category'] = __( 'カテゴリ' );
        }

        $new_columns[ $column_name ] = $column_display_name;
    }

    return $new_columns;
}

// add_filter( 'manage_{$post_type}_posts_columns' , 'add_columns' );

/*
 * https://developer.wordpress.org/reference/hooks/manage_post-post_type_posts_custom_column/
 * do_action( "manage_{$post->post_type}_posts_custom_column", string $column_name, int $post_id )
---------------------------------------------------------*/
function add_columns_value( $column, $post_id ) {

    $terms = wp_get_post_terms( $post_id, 'product-category' );

    if ( $terms && ! is_wp_error( $terms ) ) :

        $vals = array();

        foreach ( $terms as $term ) {
            $vals[] = $term->name;
        }

        $nameList = join( ",<br> ", $vals );
    endif;

    switch ( $column ) {
        case 'category' :
            echo $nameList;

            break;
    }
}

// add_action( 'manage_{$post_type}_posts_custom_column' , 'add_columns_value', 10, 2 );

/*
 *  Remove "category" from category list page url
---------------------------------------------------------- */
function remcat_function( $link ) {
    return str_replace( "/category/", "/", $link );
}

// add_filter( 'user_trailingslashit', 'remcat_function' );

function remcat_flush_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

// add_action( 'init', 'remcat_flush_rules' );

function remcat_rewrite( $wp_rewrite ) {
    $new_rules         = array( '(.+)/page/(.+)/?' => 'index.php?category_name=' . $wp_rewrite->preg_index( 1 ) . '&paged=' . $wp_rewrite->preg_index( 2 ) );
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

// add_filter( 'generate_rewrite_rules', 'remcat_rewrite' );


/*
 *
---------------------------------------------------------- */
// Activate
// add_theme_support( 'menus' );

// Define place
// register_nav_menu( 'header-nav', 'header-nav' );
// register_nav_menu( 'footer-nav', 'footer-nav' );


/*
 *
---------------------------------------------------------- */
// Add slug to <li> tag.
// https://www.warna.info/archives/1845/



/**
 *
 *
 *
---------------------------------------------------------- */
function disable_block_editor( $use_block_editor, $post_type ) {
    if ( $post_type === 'page' ) {
        return false;
    }

    return $use_block_editor;
}

add_filter( 'use_block_editor_for_post_type', 'disable_block_editor', 10, 2 );


/**
 * Disable gutenberg editor
 *
 *
---------------------------------------------------------- */
// add_filter( 'use_block_editor_for_post', '__return_false' );


/**
 *
 *
 *
---------------------------------------------------------- */
function my_admin_script() {
    global $post, $wp_query;

    if ( $post->post_type === "news" ) {
        wp_enqueue_style( 'my_admin_style', get_template_directory_uri() . '/admin.css' );
        wp_enqueue_script( 'my_admin_script', get_template_directory_uri() . '/admin.js', array( 'jquery' ), '', true );
    } elseif ( $wp_query->query_vars["post_type"] === "page" || $post->post_type === "page" ) {
        wp_enqueue_style( 'my_admin_style', get_template_directory_uri() . '/admin-page.css' );
    }
}

// add_action( 'admin_enqueue_scripts', 'my_admin_script' );


/*
 *
---------------------------------------------------------- */
function add_query_vars_filter( $vars ) {
    $vars[] = "xxx";

    return $vars;
}

// add_filter( 'query_vars', 'add_query_vars_filter' );


add_action('acf/init', 'my_acf_op_init');
function my_acf_op_init() {

    // Check function exists.
    if( function_exists('acf_add_options_page') ) {

        // Register options page.
        $option_page = acf_add_options_page(array(
            'page_title'    => __('Sự kiện'),
            'menu_title'    => __('Thêm sự kiện'),
            'menu_slug'     => 'them-su-kien',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));
    }
}


add_filter('wpcf7_autop_or_not', '__return_false');
