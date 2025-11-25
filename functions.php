<?php
/**
 * GAIA 子テーマ用 functions.php
 */

/**
 * 親テーマ＋子テーマの style.css を読み込む
 */
function gaia_child_enqueue_styles() {

	// 親テーマのスタイル
	wp_enqueue_style(
		'gaia-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	// 子テーマの style.css
	wp_enqueue_style(
		'gaia-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'gaia-parent-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'gaia_child_enqueue_styles' );

/**
 * /css/main.css（SCSSコンパイル後のCSS）を最後に読み込む
 */
function gaia_child_enqueue_custom_css() {
	$css_path = get_stylesheet_directory() . '/css/main.css';

	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'gaia-child-main-css',
			get_stylesheet_directory_uri() . '/css/main.css',
			array( 'gaia-child-style' ), // 子テーマのスタイルに依存
			filemtime( $css_path )
		);
	}
}
// 優先度を 999 にして最後に読み込む
add_action( 'wp_enqueue_scripts', 'gaia_child_enqueue_custom_css', 999 );

/**
 * カスタム投稿タイプ「キャスト」を登録
 */
function register_cast_post_type() {
	$labels = array(
		'name'               => 'キャスト',
		'singular_name'      => 'キャスト',
		'menu_name'          => 'キャスト',
		'add_new'            => '新規追加',
		'add_new_item'       => '新しいキャストを追加',
		'edit_item'          => 'キャストを編集',
		'new_item'           => '新しいキャスト',
		'view_item'          => 'キャストを表示',
		'search_items'       => 'キャストを検索',
		'not_found'          => 'キャストが見つかりませんでした',
		'not_found_in_trash' => 'ゴミ箱にキャストはありません',
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'has_archive'         => true,
		'menu_icon'           => 'dashicons-groups',
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'rewrite'             => array( 'slug' => 'cast' ),
		'show_in_rest'        => true, // ブロックエディタ対応
		'menu_position'       => 5,
	);

	register_post_type( 'cast', $args );
}
add_action( 'init', 'register_cast_post_type' );

/**
 * キャスト用カスタムフィールドを追加
 */
function add_cast_meta_boxes() {
	add_meta_box(
		'cast_details',
		'キャスト詳細情報',
		'cast_details_callback',
		'cast',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'add_cast_meta_boxes' );

/**
 * カスタムフィールドの表示
 */
function cast_details_callback( $post ) {
	wp_nonce_field( 'cast_details_nonce', 'cast_details_nonce' );
	
	// 既存の値を取得
	$birthday = get_post_meta( $post->ID, 'cast_birthday', true );
	$birthplace = get_post_meta( $post->ID, 'cast_birthplace', true );
	$height = get_post_meta( $post->ID, 'cast_height', true );
	$hobbies = get_post_meta( $post->ID, 'cast_hobbies', true );
	$skills = get_post_meta( $post->ID, 'cast_skills', true );
	$message = get_post_meta( $post->ID, 'cast_message', true );
	$instagram = get_post_meta( $post->ID, 'cast_instagram', true );
	$twitter = get_post_meta( $post->ID, 'cast_twitter', true );
	$facebook = get_post_meta( $post->ID, 'cast_facebook', true );
	?>
	
	<style>
		.cast-field { margin-bottom: 20px; }
		.cast-field label { display: block; font-weight: bold; margin-bottom: 5px; }
		.cast-field input[type="text"],
		.cast-field input[type="url"],
		.cast-field input[type="number"],
		.cast-field textarea {
			width: 100%;
			padding: 8px;
			border: 1px solid #ddd;
			border-radius: 4px;
		}
		.cast-field textarea { min-height: 120px; }
		.cast-field-description { font-size: 12px; color: #666; margin-top: 5px; }
	</style>
	
	<div class="cast-field">
		<label for="cast_birthday">誕生日</label>
		<input type="text" id="cast_birthday" name="cast_birthday" value="<?php echo esc_attr( $birthday ); ?>" placeholder="例: 8月10日" />
	</div>
	
	<div class="cast-field">
		<label for="cast_birthplace">出身</label>
		<input type="text" id="cast_birthplace" name="cast_birthplace" value="<?php echo esc_attr( $birthplace ); ?>" placeholder="例: 三重県" />
	</div>
	
	<div class="cast-field">
		<label for="cast_height">身長（cm）</label>
		<input type="number" id="cast_height" name="cast_height" value="<?php echo esc_attr( $height ); ?>" placeholder="例: 154" />
	</div>
	
	<div class="cast-field">
		<label for="cast_hobbies">趣味</label>
		<input type="text" id="cast_hobbies" name="cast_hobbies" value="<?php echo esc_attr( $hobbies ); ?>" placeholder="例: 歌うこと、踊ること、インテリア" />
	</div>
	
	<div class="cast-field">
		<label for="cast_skills">特技</label>
		<input type="text" id="cast_skills" name="cast_skills" value="<?php echo esc_attr( $skills ); ?>" placeholder="例: 甘いスイーツなら底なしに食べれる、ダンス" />
	</div>
	
	<div class="cast-field">
		<label for="cast_message">Message</label>
		<textarea id="cast_message" name="cast_message" placeholder="メッセージを入力してください"><?php echo esc_textarea( $message ); ?></textarea>
	</div>
	
	<hr style="margin: 30px 0;" />
	<h3 style="margin-bottom: 15px;">SNSリンク</h3>
	
	<div class="cast-field">
		<label for="cast_instagram">Instagram URL</label>
		<input type="url" id="cast_instagram" name="cast_instagram" value="<?php echo esc_url( $instagram ); ?>" placeholder="https://www.instagram.com/username/" />
	</div>
	
	<div class="cast-field">
		<label for="cast_twitter">X (Twitter) URL</label>
		<input type="url" id="cast_twitter" name="cast_twitter" value="<?php echo esc_url( $twitter ); ?>" placeholder="https://x.com/username" />
	</div>
	
	<div class="cast-field">
		<label for="cast_facebook">Facebook URL</label>
		<input type="url" id="cast_facebook" name="cast_facebook" value="<?php echo esc_url( $facebook ); ?>" placeholder="https://www.facebook.com/username" />
	</div>
	
	<?php
}

/**
 * カスタムフィールドの保存
 */
function save_cast_details( $post_id ) {
	// ノンスチェック
	if ( ! isset( $_POST['cast_details_nonce'] ) || ! wp_verify_nonce( $_POST['cast_details_nonce'], 'cast_details_nonce' ) ) {
		return;
	}
	
	// 自動保存の場合は処理しない
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	// 権限チェック
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	// 各フィールドを保存
	$fields = array(
		'cast_birthday',
		'cast_birthplace',
		'cast_height',
		'cast_hobbies',
		'cast_skills',
		'cast_message',
		'cast_instagram',
		'cast_twitter',
		'cast_facebook',
	);
	
	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			$value = sanitize_text_field( $_POST[ $field ] );
			
			// URLフィールドは専用のサニタイズ
			if ( strpos( $field, 'instagram' ) !== false || 
			     strpos( $field, 'twitter' ) !== false || 
			     strpos( $field, 'facebook' ) !== false ) {
				$value = esc_url_raw( $_POST[ $field ] );
			}
			
			// メッセージは改行を保持
			if ( $field === 'cast_message' ) {
				$value = sanitize_textarea_field( $_POST[ $field ] );
			}
			
			update_post_meta( $post_id, $field, $value );
		}
	}
}
add_action( 'save_post_cast', 'save_cast_details' );

/**
 * TCDテーマオプションにキャスト用設定を追加
 */
function add_cast_theme_options_to_db() {
	$options = get_option( 'theme_gaia_options', array() );
	
	$cast_defaults = array(
		'cast_label' => 'キャスト',
		'cast_archive_headline' => 'CAST',
		'cast_archive_desc' => '',
		'cast_archive_desc_sp' => '',
		'cast_archive_image' => '',
		'cast_archive_overlay_color' => '#000000',
		'cast_archive_overlay_opacity' => 0.3,
	);
	
	$updated = false;
	foreach ( $cast_defaults as $key => $value ) {
		if ( ! isset( $options[ $key ] ) ) {
			$options[ $key ] = $value;
			$updated = true;
		}
	}
	
	if ( $updated ) {
		update_option( 'theme_gaia_options', $options );
	}
}
add_action( 'init', 'add_cast_theme_options_to_db' );