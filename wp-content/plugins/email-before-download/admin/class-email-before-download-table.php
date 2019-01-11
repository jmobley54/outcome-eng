<?php
/**
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/admin
 * @author     M & S Consulting
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Email_Before_download_Table extends WP_List_Table
{

    public $atts;
    public $db;

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'ebd_download',
            'plural' => 'ebd_downloads',
            'ajax' => false
        ));
        global $wpdb;
        $this->db = $wpdb;
    }

    function get_columns()
    {
        return $this->atts['columns'];
    }

    public function get_sortable_columns()
    {
        return $this->atts['sortable'];
    }


    function prepare_items()
    {
        global $_wp_column_headers;
        $table = $this->atts['table'];
        $screen = get_current_screen();
        $query = "SELECT * FROM $table";
        $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : '';
            $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : '';
        if (!empty($orderby) & !empty($order)) {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }else {
            $query .= " ORDER BY time_requested DESC";
        }

        $totalitems = $this->db->query($query);
        $perpage = 25;
        $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
            $this->set_pagination_args(array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage,
            ));
            $columns = $this->get_columns();
            $_wp_column_headers[$screen->id] = $columns;
            $this->_column_headers = array(
                $this->get_columns(),
                array(),
                $this->get_sortable_columns(),
            );
            $this->items = $this->db->get_results($query);
        }
    }

    function display_rows()
    {
        $records = $this->items;
        list($columns, $hidden) = $this->get_column_info();
        if (!empty($records)) {
            foreach ($records as $rec) {
                if (isset($rec->id)) {
                    echo '<tr id="record_' . $rec->id . '">';
                } else {
                    echo '<tr class="record">';
                }
                foreach ($columns as $column_name => $column_display_name) {
                    //Style attributes for each col
                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if (in_array($column_name, $hidden)) $style = ' style="display:none;"';
                    $attributes = $class . $style;
                    if ($column_name == "is_downloaded") {
                        $rec->$column_name = ($rec->$column_name < 1 ? 'No' : 'Yes');
                    } elseif ($column_name == "expire_time") {
                        $rec->$column_name = ($rec->$column_name ? $rec->$column_name : "Doesn't Expire");
                        if ($rec->$column_name != "Doesn't Expire") {
                            if (time()  > $rec->$column_name) {
                                $rec->$column_name = "<span style='color:red'>".date('Y-m-d H:i:s', stripslashes($rec->$column_name))."</span>";
                            }
                        }
                    } elseif ($column_name == "posted_data") {
                        $rec->$column_name = "<div class='ebd_xml'>" . htmlentities($rec->$column_name) . "</div>";
                    }
                    if ($this->isValidTimeStamp($rec->$column_name)) {
                        echo '<td ' . $attributes . '>' . date('Y-m-d H:i:s', stripslashes($rec->$column_name)) . '</td>';
                    } else {
                        echo '<td ' . $attributes . '>' . stripslashes($rec->$column_name) . '</td>';
                    }
                }
                echo '</tr>';
            }
        }
    }

    public function set_table($table)
    {
        if ($table == 'links') {
            $this->atts = array(
                'table' => $this->db->prefix . "ebd_link",
                'columns' => array(
                    'email' => __('Email'),
                    'is_downloaded' => __('Downloaded'),
                    'time_requested' => __('Time Requested'),
                    'delivered_as' => __('Delivered As'),
                    'expire_time' => "Expires"
                ),
                'sortable' => array(
                    'email' => array('email', true),
                    'is_downloaded' => array('is_downloaded', true),
                    'time_requested' => array('time_requested', true),
                    'delivered_as' => array('delivered_as', true),
                    'expire_time' => array('expire_time', true),
                ),
                'purge_text' => __("Purge Expired Links",'email-before-download')
            );
        } else {
            $this->atts = array(
                'table' => $this->db->prefix . "ebd_posted_data",
                'columns' => array(
                    'email' => __('Email'),
                    'user_name' => __('Name'),
                    'time_requested' => __('Time Requested'),
                    'posted_data' => "XML Data"
                ),
                'sortable' => array(
                    'email' => array('email', true),
                    'is_downloaded' => array('is_downloaded', true),
                    'time_requested' => array('time_requested', true),
                ),
                'purge_text' => __("Purge Logs",'email-before-download')
            );
        }

    }

    public function isValidTimeStamp($timestamp)
    {
        return ((string)(int)$timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX)
            && (strlen($timestamp) > 9);
    }
}
