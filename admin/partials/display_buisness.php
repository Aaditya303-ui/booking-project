<?php

global $wpdb;

$table_name = $wpdb->prefix . 'business_hours';

$query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE 1=1");

$results = $wpdb->get_results($query);

$data = array();

foreach ($results as $row) {

    $open      = $row->open_time;
    $close     = $row->close_time;

    $open_time  = date("g:i A", strtotime($open));
    $close_time = date("g:i A", strtotime($close));

    $data[] = array(
        'id'         => $row->id,
        'day'        => $row->day_of_week,
        'open_time'  => $open_time,
        'close_time' => $close_time,
        'is_closed'  => $row->is_closed
    );
}
?>
<div class="business-wrap">

    <h1>Booking Settings</h1>
    <h2>Business Hours</h2>

    <p>
        Configure the days and times when bookings are allowed.
    </p>

    <table class="business-table">
        <thead>
            <tr>
                <th>Day</th>
                <th>Status</th>
                <th>Opening Time</th>
                <th>Closing Time</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody id="mycontent3">
            <?php

            $i = 0;

            while ($i < count($data)) {
                $current_day = trim(strtolower($data[$i]['day']));
                if ($current_day === "sunday" || $current_day === "saturday") {
                    $i++;
                    continue;
                }

                $open_time   = strtotime($data[$i]['open_time']);
                $u_open_time = date("H:i", $open_time);

                $close_time   = strtotime($data[$i]['close_time']);
                $u_close_time = date("H:i", $close_time);
            ?>

                <tr>
                    <td><?php echo $data[$i]['id'] ?></td>
                    <td><?php echo $data[$i]['day'] ?></td>
                    <td><?php echo $data[$i]['open_time'] ?></td>
                    <td><?php echo $data[$i]['close_time']; ?></td>
                    <td>
                        <button class="view-buisness-link"
                            data-id="<?php echo $data[$i]['id'] ?>">
                            update
                        </button>
                    </td>
                    <div
                        id="buisness-modal-<?php echo $data[$i]['id']; ?>"
                        class="booking-modal-overlay"
                        style="display:none;">
                        <div class="booking-modal-content">

                            <span class="booking-modal-close">×</span>

                            <h2>Select Time</h2>

                            <input 
                            type="hidden" 
                            id="u_start_time-<?php echo $data[$i]['id']; ?>"
                            value="<?php echo $u_open_time; ?>">

                            <input 
                            type="hidden"
                            id="u_close_time-<?php echo $data[$i]['id'] ?>"
                            value="<?php echo $u_close_time; ?>">

                            <div class="booking-field">
                                <label>Day</label>
                                <h3><?php echo $data[$i]['day']; ?></h3>
                            </div>

                            <div class="booking-field">
                                <label>Start Time</label>
                                <input
                                    type="time"
                                    id="start-time-<?php echo $data[$i]['id']; ?>"
                                    value="<?php echo $u_open_time; ?>">
                            </div>

                            <div class="booking-field">
                                <label>End Time</label>
                                <input
                                    type="time"
                                    id="end-time-<?php echo $data[$i]['id']; ?>"
                                    value="<?php echo $u_close_time; ?>">
                            </div>

                            <button
                                data-id="<?php echo $data[$i]['id'] ?>"
                                id="submit-buiseness-btn"
                                type="button">
                                Save
                            </button>

                        </div>
                    </div>
                </tr>
            <?php
                $i++;
            } ?>
        </tbody>
    </table>

    <div class="business-actions"></div>

</div>