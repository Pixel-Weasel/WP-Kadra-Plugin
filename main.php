<?php
/*
Plugin Name: ZSPlugin
Plugin URI: https://zs-belzyce.edu.pl/
Description: Plugin do wyświetlania przedmiotów nauczanych przez nauczycieli oraz kadry.
Version: 1.4
Author: <a href="https://github.com/KaminskiDaniell">Daniel Kamiński</a>, <a href="https://github.com/jakubZlotek">Jakub Złotek</a>
Author URI: https://jajuwa.xyz/
License: MIT
*/

/*
┌──────────────────────────────────────────────────────┐
│                                                      │
│ Dodanie inputów do dodania meta tagu dla nauczyciela │
│                                                      │
└──────────────────────────────────────────────────────┘
*/
add_action( 'show_user_profile', 'admin_teacher_description' );
add_action( 'edit_user_profile', 'admin_teacher_description' );
add_action( 'user_new_form', 'admin_teacher_description' );
function admin_teacher_description( $operation ) {
	if ( 'add-new-user' !== $operation ) {
		return;
	}
	?>
	<h3><?php _e("Dodatkowe informacje o nauczycielu", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="subjects"><?php _e("Nauczane przedmioty"); ?></label></th>
        <td>
            <input type="text" name="subjects" id="subjects" value="<?php echo esc_attr( get_the_author_meta( 'subjects', $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
    </tr>
    <tr>
        <th><label for="position"><?php _e("Stanowisko"); ?></label></th>
        <td>
            <input type="text" name="position" id="position" value="<?php echo esc_attr( get_the_author_meta( 'position', $user->ID ) ); ?>" class="regular-text" /><br />
            <p class="description">Przed stanowiskiem podaj numer, na podstawie którego będzie określona kolejność np. (1. Dyrektor, 2. V-ce Dyrektor) itd.</p>
        </td>
    </tr>
    </table>
	<?php
}

add_action('user_register', 'save_teacher_description');
add_action('profile_update', 'save_teacher_description');
function save_teacher_description($user_id){
    # again do this only if you can
    if(!current_user_can('manage_options'))
        return false;

    # save my custom field
    update_user_meta($user_id, 'subjects', $_POST['subjects']);
    update_user_meta($user_id, 'position', $_POST['position']);
}


add_action( 'user_register', 'teacher_description_register' );

function teacher_description_register( $user_id ) {
	if ( ! empty( $_POST['subjects'] ) ) {
		update_user_meta( $user_id, 'subjects', intval( $_POST['subjects'] ) );
    }
    if ( ! empty( $_POST['position'] ) ) {
		update_user_meta( $user_id, 'position', intval( $_POST['position'] ) );
    }
}

add_action( 'show_user_profile', 'teachers_description' );
add_action( 'edit_user_profile', 'teachers_description' );

function teachers_description( $user ) { ?>
    <h3><?php _e("Dodatkowe informacje o nauczycielu", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="subjects"><?php _e("Nauczane przedmioty"); ?></label></th>
        <td>
            <input type="text" name="subjects" id="subjects" value="<?php echo esc_attr( get_the_author_meta( 'subjects', $user->ID ) ); ?>" class="regular-text" /><br />
        </td>
    </tr>
    <tr>
        <th><label for="position"><?php _e("Stanowisko"); ?></label></th>
        <td>
            <input type="text" name="position" id="position" value="<?php echo esc_attr( get_the_author_meta( 'position', $user->ID ) ); ?>" class="regular-text" /><br />
            <p class="description">Przed stanowiskiem podaj numer, na podstawie którego będzie określona kolejność np. (1. Dyrektor, 2. V-ce Dyrektor) itd.</p>
        </td>
    </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'save_teachers_description' );
add_action( 'edit_user_profile_update', 'save_teachers_description' );

function save_teachers_description( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }
    update_user_meta( $user_id, 'subjects', $_POST['subjects'] );
    update_user_meta( $user_id, 'position', $_POST['position'] );
}

/*
┌──────────────────────────────────────────────────────┐
│                                                      │
│       Dodanie kolumn do sekcji użytkowników          │
│                                                      │
└──────────────────────────────────────────────────────┘
*/
function register_custom_user_column($columns) {
    $columns['subjects'] = 'Nauczane przedmioty';
    $columns['position'] = 'Stanowisko';
    return $columns;
}
function register_custom_user_column_view($value, $column_name, $user_id) {
    $subjects = get_usermeta( $user_id, "subjects" );
    if ($subjects == "0") {
        $subjects = "—";
    }
    $position = get_usermeta( $user_id, "position" );
    if ($position == "0") {
        $position = "—";
    }
    if($column_name == 'subjects') return $subjects;
    if($column_name == 'position') return $position;
    return $value;

}
add_action('manage_users_columns', 'register_custom_user_column');
add_action('manage_users_custom_column', 'register_custom_user_column_view', 10, 3);

/*
┌──────────────────────────────────────────────────────────────────────────────────┐
│                                                                                  │
│       Dodanie customowego bloku wyświetlającego nauczycieli do Gutenberga        │
│                                                                                  │
└──────────────────────────────────────────────────────────────────────────────────┘
*/
function register_teacher_subjects_block() {

	// Check if Gutenberg is active.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Add block script.
	wp_register_script(
		'call-to-action',
		plugins_url( 'blocks/call-to-action.js', __FILE__ ),
		[ 'wp-blocks', 'wp-element', 'wp-editor' ],
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks/call-to-action.js' )
	);

	// Add block style.
	wp_register_style(
		'call-to-action',
		plugins_url( 'blocks/call-to-action.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks/call-to-action.css' )
	);

	// Register block script and style.
	register_block_type( 'mcb/call-to-action', [
		'style' => 'call-to-action', // Loads both on editor and frontend.
        'editor_script' => 'call-to-action', // Loads only on editor.
        'render_callback' => 'teacherSubjectRenderer',
	] );
}

function teacherSubjectRenderer($attributes) {
    $args = array(
        'orderby'   => 'meta_value',
        'meta_key' => 'position',
        'order' => 'ASC',
    );
    $authors_query = new WP_User_Query( $args );
    foreach ($authors_query->results as $author) {
        $user = get_userdata($author->ID);
        $user_position = get_user_meta($author->ID, "position", 1);
        if (strlen($user_position) < 2) continue;
        $user_position = preg_replace('/[0-9.]+/', '', $user_position);
        $position .= "<h4 class='has-text-align-center'>" . $user_position . " - " . $user->first_name . " " .  $user->last_name  . "</h4>";
    }

    $args = array(
        'meta_key' => 'last_name',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'role' => 'author'
    );
    $user_query = new WP_User_Query( $args );
    foreach($user_query->results as $user){
        $author_info = get_userdata($user->ID);
        $author_subjects = get_user_meta($user->ID, "subjects", 1);
        if ($author_subjects == "0") $author_subjects = " ";
        $content .= "<tr><td>" . $author_info->last_name . " " . $author_info->first_name . "</td><td>" . $author_subjects . "</td></tr>";
    }
    $subtitle .= '<h3 class="has-text-align-center "><b>'. $attributes[ 'content' ] .'</b></h3>';
    $subtitle .= $position;
    $subtitle .= '<div class="wp-block-group"><div class="wp-block-group__inner-container"><div class="wp-block-getwid-table alignwide has-table-layout-fixed has-horizontal-align-center"><table class="has-text-align-center"><tbody><tr><td>Imię i Nazwisko:</td><td>Nauczane przedmioty:</td></tr>';
    $subtitle .= $content;
    $subtitle .= '</tbody></table></div></div></div>';
                    
    return $subtitle;
}

add_action( 'init', 'register_teacher_subjects_block' );

/*
┌──────────────────────────────────────────────────────────────────────────────────┐
│                                                                                  │
│       Dodanie customowego bloku wyświetlającego podstrony dla danej strony       │
│                                                                                  │
└──────────────────────────────────────────────────────────────────────────────────┘
*/
function register_subpages_block() {

	// Check if Gutenberg is active.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Add block script.
	wp_register_script(
		'subpages',
		plugins_url( 'blocks/subpages.js', __FILE__ ),
		[ 'wp-blocks', 'wp-element', 'wp-editor' ],
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks/subpages.js' )
	);

	// Add block style.
	wp_register_style(
		'subpages',
		plugins_url( 'blocks/subpages.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'blocks/subpages.css' )
	);

	// Register block script and style.
	register_block_type( 'mcb/subpages', [
		'style' => 'subpages', // Loads both on editor and frontend.
        'editor_script' => 'subpages', // Loads only on editor.
        'render_callback' => 'subpagesRenderer',
	] );
}

function subpagesRenderer() {
    global $post; 
    if ( is_page() && $post->post_parent ) {
        $childpages = wp_list_pages( 'sort_column=post_name&title_li=&child_of=' . $post->ID . '&echo=0' );
    }    
    if ( $childpages ) {
        $content = '<ol>' . $childpages . '</ol>';
    }

    return $content; 
}

add_action( 'init', 'register_subpages_block' );

/*
┌──────────────────────────────────────────────────────────────────────────────────┐
│                                                                                  │
│                         Blok dodający customowy widget                           │
│                                                                                  │
└──────────────────────────────────────────────────────────────────────────────────┘
*/

class Free_Days_Widget extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'free-days-widget',  // Base ID
            'Dni Wolne Widget'   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'Free_Days_Widget' );
        });
 
    }
 
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
 
        echo '<div class="textwidget">';
 
        echo esc_html__( $instance['text'], 'text_domain' );
 
        echo '</div>';
 
        echo $args['after_widget'];
 
    }
 
    public function form( $instance ) {
 
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        $text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'Text' ) ); ?>"><?php echo esc_html__( 'Text:', 'text_domain' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?></textarea>
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
 
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['text'] = ( !empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';
 
        return $instance;
    }
 
}
$my_widget = new Free_Days_Widget();