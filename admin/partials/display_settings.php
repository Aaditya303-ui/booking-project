<?php

global $wpdb;

$table_name = $wpdb->prefix . 'blackout';
$limit = 4;

if (isset($_GET['page']) && absint($_GET['page']) !== 0) {
    $pn = absint($_GET['page']);
} else {
    $pn = 1;
};

$start_from = absint(($pn - 1) * $limit);

$query  = $wpdb->prepare("SELECT * FROM {$table_name} WHERE 1=1
LIMIT $start_from, $limit");
$query1 = $wpdb->prepare("SELECT COUNT(*) as counts FROM {$table_name} WHERE 1=1
                            LIMIT $start_from, $limit");

$results  = $wpdb->get_results($query);
$results1 = $wpdb->get_results($query1);

$data = array();
$count = $results1[0]->counts;

foreach ($results as $row) {
    $arr  = explode(" ", $row->blackout_date);
    $date = $arr[0];
    $new_date = new DateTime($date);
    $date_format = $new_date->format('l, F j, Y');

    $data[] = array(
        'blackout_id'       => $row->blackout_id,
        'blackout_date'     => $row->blackout_date,
        'reason'            => $row->reason,
        'date'              => $date_format,
        'exdate'            => $date
    );
}
?>
<div class="blackout-wrap">

    <div class="blackout-header">
        <div>
            <h1>Blackout Dates</h1>
        </div>
        <div>
            <input type="text" id="search_reason">
            <button id="searchbtn">search</button>
            <input type="date" id="select_date">
            <select id="sort_by">
                <option value="">select the sorting</option>
                <option value="old">old</option>
                <option value="new">new</option>
                <option value="a-z">a-z</option>
                <option value="z-a">z-a</option>
            </select>
            <select id="month">
                <option value="">Select the Month</option>
                <option value="january">January</option>
                <option value="february">February</option>
                <option value="march">March</option>
                <option value="april">April</option>
                <option value="may">May</option>
                <option value="june">June</option>
                <option value="july">July</option>
                <option value="august">August</option>
                <option value="september">September</option>
                <option value="october">October</option>
                <option value="november">November</option>
                <option value="december">December</option>
            </select>
        </div>
        <button type="button" id="add-blackout-btn" class="blackout-add-btn">
            + Add Date
        </button>
    </div>

    <div class="blackout-table-container">
        <table class="blackout-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Blackout Date</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="mycontent2">
                <?php

                $i = 0;
                while ($i < count($data)) {
                    $id       = absint($data[$i]['blackout_id']);
                    $blackout = esc_html($data[$i]['date']);
                    $reason   = esc_html($data[$i]['reason']);
                ?>

                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $blackout ?></td>
                        <td><?php echo $reason; ?></td>
                        <td>
                            <button id="delete-btn" data-id=<?php echo $id; ?>>
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php
                    $i++;
                } ?>
            </tbody>
        </table>
        <div id="paginate1">
            <div id="pagination-link">
                <?php
                $countsql = "SELECT COUNT(*) as counts FROM $table_name";
                $query1 = $wpdb->prepare($countsql);
                $results  = $wpdb->get_results($query1);

                $total_records = $results[0]->counts;
                $total_pages = ceil($total_records / $limit);

                $pagLink = "";
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $pn) {
                        $pagLink .= "<li class='active' >
            <a id='page1' data-lid=" . $i . ">" . $i . "</a>
            </li>";
                    } else {
                        $pagLink .= "<li>
                            <a id='page1' data-lid=" . $i . ">"
                            . $i .
                            "</a>";
                    }
                };
                echo $pagLink;
                ?>
            </div>
        </div>
    </div>

    <p id="blackout-empty-message" class="blackout-empty" style="display:none;">
        No blackout dates have been added yet.
    </p>

</div>


<div id="blackout-modal" style="display: none;">
    <form id="blackout-form">

        <h3>Add Blackout Date</h3>

        <label for="blackout_date">Date</label>
        <input type="date" id="blackout_date" required>

        <br><br>

        <label for="blackout_reason">Reason</label>
        <input type="text" id="blackout_reason"
            placeholder="Enter reason">

        <br><br>

        <button type="submit">Save Date</button>
        <button type="button" id="cancel-blackout">Cancel</button>

    </form>
</div>