diff --git a/functions.php b/functions.php
index 1920e140b4fc9f07f993ae2b2c95fc9cef9da99e..ff2e0686cb9725e3da4f3cf7d4f4287bb35f2383 100644
--- a/functions.php
+++ b/functions.php
@@ -213,50 +213,78 @@ function save_cast_details( $post_id ) {
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
-	$options = get_option( 'theme_gaia_options', array() );
+$options = get_option( 'theme_gaia_options', array() );
 	
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
 	
-	if ( $updated ) {
-		update_option( 'theme_gaia_options', $options );
-	}
+        if ( $updated ) {
+                update_option( 'theme_gaia_options', $options );
+        }
+}
+add_action( 'init', 'add_cast_theme_options_to_db' );
+
+/**
+ * テーマオプションの読み込み時にキャスト用キーのデフォルトを保証
+ *
+ * 親テーマのテンプレートパーツでは `$dp_options['cast_label']` などの
+ * 追加キーを前提にしているため、未設定の場合に Undefined array key
+ * 警告が出る。`option_{$option}` フィルターで読み込み時に不足分を
+ * 補完することで警告を抑制し、常に期待されるキーが存在するように
+ * する。
+ */
+function gaia_child_filter_cast_theme_options( $options ) {
+        if ( ! is_array( $options ) ) {
+                $options = array();
+        }
+
+        $cast_defaults = array(
+                'cast_label'                   => 'キャスト',
+                'cast_archive_headline'        => 'CAST',
+                'cast_archive_desc'            => '',
+                'cast_archive_desc_sp'         => '',
+                'cast_archive_image'           => '',
+                'cast_archive_overlay_color'   => '#000000',
+                'cast_archive_overlay_opacity' => 0.3,
+        );
+
+        return array_merge( $cast_defaults, $options );
 }
-add_action( 'init', 'add_cast_theme_options_to_db' );
\ No newline at end of file
+add_filter( 'option_theme_gaia_options', 'gaia_child_filter_cast_theme_options' );
\ No newline at end of file
