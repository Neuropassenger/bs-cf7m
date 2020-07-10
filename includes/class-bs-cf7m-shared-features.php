<?php
class Bs_Cf7m_Shared_Features {
    public static function bs_logit( $data, $description ) {
        $filename = WP_CONTENT_DIR . '/bs_log.txt';

        $text = "===[ " . $description . " ]===\n";
        $text .= "===[ " . date( 'M d Y, G:i:s', time() ) . " ]===\n";
        $text .= print_r( $data, true ) . "\n";
        $file = fopen( $filename, 'a' );
        fwrite( $file, $text );
        fclose( $file );
    }
}