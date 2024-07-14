<?php
/**
 * Plugin Name: Movi
 * Plugin URI: https://github.com/WordPress/wordpress-develop/blob/master/wp-includes/Plugin.php#L11
 * Description: This is a basic practice plugin for generating QR codes.
 * Version: 1.0.0
 * Author: Sohidul Islam Apu
 * Author URI: https://wordpress.org/plugins/our-first-plugin
 */

 class Movi {
     public function __construct() {
        add_action( 'init', [$this, 'register_post_type'] );
        add_action( 'init', [$this, 'register_taxonomies'] );
        add_filter( 'the_content', [$this, 'post_by_genre_taxonomy']);
        add_filter( 'the_content',[$this,'series_cast_in_episodes']);
        add_filter( 'the_content', [$this, 'related_movis'] );
    }

    
    public function related_movis($content){
        if(!is_singular('movi')){
            return $content;
        }

        $get_id = get_the_ID();

        $argc = [  
            'post_type' => 'movi',
            'post__not_in' => [$get_id]
        ];
        $get_posts = get_posts($argc);
        if($get_posts){
            $content .= '<ul> <h4>Related Movis</h4>';
            foreach($get_posts as $post){
                $thumbnail = get_the_post_thumbnail($post->ID,'thumbnail');
                $permalink = get_permalink($post->ID);
                $content .= "<li>{$thumbnail}</li>";
                $content .= "<li><a href='{$permalink}'>{$post->post_title}</a></li>";
            }   
            $content .= '</ul>';
        }

        return $content;
    }
    public function post_by_genre_taxonomy($content){
        if(!is_singular('movi')){
            return $content;
        }

        $genre      = get_the_term_list( get_the_ID(), 'genre', '', ',', '' );
        $actor      = get_the_term_list( get_the_ID(), 'actor', '', ',', '' );
        $director   = get_the_term_list( get_the_ID(), 'director', '', ',', '' );
        $year       = get_the_term_list( get_the_ID(), 'year', '', ',', '' );

        $content    .= '<ul>';

        if($genre){
            $content    .= "<li>Genre : {$genre}</li>";
        }
        if($actor){
            $content    .= "<li>Actor : {$actor}</li>";
        }
        if($director){
            $content    .= "<li>Director : {$director}</li>";
        }
        if($year){
            $content    .= "<li>Year : {$year}</li>";
        }
        $content    .= '</ul>';
        
        return $content;
    }

    public function register_post_type() {
        register_post_type( 'movi', [
            'label' => 'Movi',
            'Labels' => [
                'name' => 'Movi',
                'singular_name' => 'Movi',
                'add_new' => 'Add New Movi',
            ],
            'public' => true,
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'taxonomies' => [ 'genre', 'actor', 'director', 'year' ],
            'show_admin_column' => true,

        ] );
        register_post_type( 'seriescast', [
            'label' => 'Series Cast',
            'Labels' => [
                'name' => 'Series Cast',
                'singular_name' => 'Series Cast',
                'add_new' => 'Add New Series Cast',
            ],
            'public' => true,
            'supports' => [ 'title', 'editor', 'thumbnail' ],

        ] );

    }

    public function register_taxonomies() {
        register_taxonomy('genre','movi',[
            'labels' => [
                'name' => 'Genre',
                'singular_name' => 'Genre',
                'add_new' => 'Add New Genre',
            ],
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
        ]);

        register_taxonomy('actor','movi',[
            'labels' => [
                'name' => 'Actor',
                'singular_name' => 'Actor',
                'add_new' => 'Add New Actor',
            ],
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,

        ]);

        register_taxonomy('director','movi',[
            'labels' => [
                'name' => 'Director',
                'singular_name' => 'Director',
                'add_new' => 'Add New Director',
            ],
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,

        ]);
        register_taxonomy('year','movi',[
            'labels' => [
                'name' => 'Year',
                'singular_name' => 'Year',
                'add_new' => 'Add New Year',
            ],
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,

        ]);
    }
    public function series_cast_in_episodes($content){

        if(!is_singular('movi')){
            return $content;
        }
        $get_id = get_the_ID();
        
        $argc = [
            'post_type' => 'seriescast',
            'meta_query' => [
                [
                    'key' => 'movi',
                    'value' => $get_id
                ]
            ]
        ];

        $get_posts = get_posts($argc);

        $content .= '<ul> <h4>Series Cast</h4>';
        if($get_posts){
            foreach($get_posts as $post){
                $thumbnail = get_the_post_thumbnail($post->ID,'thumbnail');
                $episodes = get_post_meta($post->ID, 'episodes_number', true);
                $permalink = get_permalink($post->ID);
                $content .= "<li>{$thumbnail}</li>";
                $content .= "<li><a href='{$permalink}'>{$post->post_title}</a></li>";
                $content .= "<li>Episodes Number : {$episodes}</li>";
            }
        }
        $content .= '</ul>';

        return $content;
        
    }
    
}

$movi = new Movi();