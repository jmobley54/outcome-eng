<?php
/**
 * Used for plugin specific database actions.
 *
 * This class defines most code necessary to read and write
 * links, items, and logs to the database.
 *
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */

class Email_Before_Download_DB
{
    private $db;
    private $item_table;
    private $link_table;
    private $posted_table;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->item_table = $this->db->prefix . "ebd_item";
        $this->link_table = $this->db->prefix . "ebd_link";
        $this->posted_table = $this->db->prefix . "ebd_posted_data";
    }

    public function select_item($data)
    {
        return $this->db->get_row("SELECT  * FROM $this->item_table WHERE download_id = '$data'");
    }

    public function create_item($atts)
    {
        $item = $this->item_exists($atts);
        if ($item) {
            return $item;
        } else {
            $this->db->insert($this->item_table,
                array(
                    'download_id' => $atts['download_id'],
                    'title' => sanitize_text_field($atts['title']),
                    'file' => esc_url($atts['file'])
                ));
            return $this->db->insert_id;
        }
    }

    public function item_exists($data)
    {

        //check if item exists and if it needs updated
        $query = $this->db->get_row("SELECT  * FROM $this->item_table WHERE download_id = '" . $data['download_id'] . "'");
        if (count($query) > 0) {
            if ($query->file != $data['file']) {
                $this->db->update($this->item_table,
                    array('file' => $data['file']),
                    array('id' => $query->id)
                );
            }
            if ($query->title != $data['title']) {
                $this->db->update($this->item_table,
                    array('title' => $data['title']),
                    array('id' => $query->id)
            );
            }
            return $query->id;
        }
        return false;

    }

    public function create_link($data)
    {
        $data['expire_time'] = $this->expired($data['expire_time']);
        $this->db->insert($this->link_table, $data);
        $id = $this->db->insert_id;
        $link = $this->db->get_row("SELECT  * FROM $this->link_table WHERE id = '$id'");
        return $link;
    }

    public function select_link($column, $data)
    {
        $query = $this->db->get_row("SELECT  * FROM $this->link_table WHERE $column = '$data'");
        return $query;
    }

    public function mark_downloaded($id)
    {

        return $this->db->update(
            $this->link_table,
            array('is_downloaded' => 1),
            array('ID' => $id)
        );

    }

    public function create_log($data)
    {
        return $this->db->insert($this->posted_table, $data);

    }

    public function expired($option)
    {
        if ($option != 0) {
            $duration = array(
                'minute' => 60,
                'hour' => 3600,
                'day' => 172800,
                'week' => 604800
            );

            $expired = explode(" ", $option);
            $expiration = $expired[0] * $duration[$expired[1]];
            $option = time() + $expiration;
        }
        return $option;
    }

    public function clean_up($table)
    {

        if ($table == $this->db->prefix . "ebd_link") {
            $now = time();
            $query = "DELETE FROM $table WHERE expire_time < $now";
            $page = 'links';
        } else {
            $query = "DELETE FROM $table";
            $page = "logs";
        }
        $this->db->query($query);
        session_start();
        $_SESSION['success'] = "$page have been purged.";
        wp_redirect(admin_url("/options-general.php?page=email-before-download-$page"));

    }
    public function export($table){
        $csv = "";
        foreach ( $this->db->get_col( "DESC " . $table, 0 ) as $column_name ) {
            $csv .= $column_name.",";
        }
        $csv =  substr($csv, 0, -1). "\n";
        foreach( $this->db->get_results("SELECT * FROM $table") as $key => $row) {
            foreach ($row as $key2 => $value){
                $csv .= $value. ",";
            }
            $csv =  substr($csv, 0, -1). "\n";
        }
        return $csv;
    }
    public function get_ajax_links($email){
        $time = time() - 30;

        $linkQuery = "
	SELECT uid, selected_id
	FROM $this->link_table
	WHERE email = '$email' 
		AND time_requested > $time
	";
        $links = $this->db->get_results($linkQuery);

        return $links;
    }
    public function skip_check($downloadID) {
            $sql = "
                SELECT * 
                FROM $this->item_table 
                WHERE download_id = $downloadID 
                OR download_id LIKE ',$downloadID'
                OR download_id LIKE '$downloadID,' 
                OR download_id LIKE ',$downloadID,'";

            $query = $this->db->get_results($sql);

            if(count($query) < 1){
                return false;
            }
            return true;
        }

}
